<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    private string $projectId;
    private string $keyFilePath;

    public function __construct(?string $projectId = null, ?string $keyFilePath = null)
    {
        $this->projectId = 'vyapto-ec341';
        $this->keyFilePath =  storage_path('app/public/firebase-key.json');
    }

    public function getAccessToken(): ?string
    {
        if (!is_file($this->keyFilePath)) {
            Log::error('FCM key file missing.', ['key_file_path' => $this->keyFilePath]);
            return null;
        }

        $jsonKey = json_decode((string) file_get_contents($this->keyFilePath), true);
        if (!is_array($jsonKey) || empty($jsonKey['client_email']) || empty($jsonKey['private_key'])) {
            Log::error('FCM key file invalid. Missing client_email/private_key.', ['key_file_path' => $this->keyFilePath]);
            return null;
        }

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $now = time();
        $payload = [
            'iss' => $jsonKey['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ];

        $jwtHeader = $this->base64UrlEncodeJson($header);
        $jwtPayload = $this->base64UrlEncodeJson($payload);

        $signature = '';
        $signOk = openssl_sign($jwtHeader . '.' . $jwtPayload, $signature, $jsonKey['private_key'], 'SHA256');
        if (!$signOk) {
            Log::error('FCM JWT signing failed.');
            return null;
        }

        $jwt = $jwtHeader . '.' . $jwtPayload . '.' . $this->base64UrlEncodeBinary($signature);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://oauth2.googleapis.com/token',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]),
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode((string) $response, true);
        if (!isset($result['access_token'])) {
            Log::error('Failed to get FCM access token.', [
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response' => $response,
            ]);
            return null;
        }

        Log::debug('FCM access token generated.', ['http_code' => $httpCode]);
        return $result['access_token'];
    }

    public function sendNotification(string $token, string $title, string $body, array $data = [], array $android = []): string
    {
        Log::debug('FCM sendNotification called.', [
            'project_id' => $this->projectId,
            'token_tail' => substr($token, -12),
            'title' => $title,
            'data_keys' => array_keys($data),
        ]);

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            Log::error('FCM send aborted. Access token failed.');
            return 'Access token failed';
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_map('strval', $data),
                'android' => array_merge(['priority' => 'HIGH'], $android),
            ],
        ];

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400 || $curlError !== '') {
            Log::error('FCM send failed.', [
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'response' => $response,
            ]);
        } else {
            Log::debug('FCM send success.', [
                'http_code' => $httpCode,
                'response' => $response,
            ]);
        }

        return (string) $response;
    }

    private function base64UrlEncodeJson(array $data): string
    {
        return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
    }

    private function base64UrlEncodeBinary(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
