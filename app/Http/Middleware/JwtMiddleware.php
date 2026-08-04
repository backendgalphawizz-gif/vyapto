<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::setRequest($request);
            $user = JWTAuth::parseToken()->authenticate();

            // authenticate() returns false when subject user is missing → would TypeError on setUser()
            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized: User not found',
                ], 401);
            }

            auth('api')->setUser($user);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Token expired',
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Token invalid',
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Token not provided',
            ], 401);
        }

        return $next($request);
    }
}
