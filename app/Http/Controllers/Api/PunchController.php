<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\PunchIn;
use App\Models\Api\UserToken;
use App\Services\EmployeeLocationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class PunchController extends Controller
{
    public function __construct(private EmployeeLocationService $locationService)
    {
    }

    public function punchIn(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'location' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        if (! empty($token)) {
            $userToken = UserToken::where('token', $token)->first();
            if (! $userToken) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token'], 401);
            }
        }

        $user = auth('api')->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $today = date('Y-m-d');
        $alreadyPunch = PunchIn::where('employee_id', $user->id)->whereDate('punch_in_date', $today)->first();

        if ($alreadyPunch) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => 'You have already punched in today',
                'data' => $alreadyPunch,
            ]);
        }

        $userLat = $request->latitude;
        $userLng = $request->longitude;

        $locationTarget = $this->locationService->resolveLocationTarget($user);
        if (! $locationTarget['status']) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => $locationTarget['message'],
            ]);
        }

        $distance = $this->calculateDistance(
            $userLat,
            $userLng,
            $locationTarget['latitude'],
            $locationTarget['longitude']
        );

        // Distance is in KM; 0.1 KM = 100 meters.
        if ($distance > 0.1) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => $locationTarget['mismatch_message'],
                'distance_in_meters' => round($distance * 1000, 2),
            ]);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('punch_images', 'public');
        }

        $punch = PunchIn::create([
            'employee_id' => $user->id,
            'punch_in_date' => date('Y-m-d'),
            'punch_in_time' => Carbon::now(),
            'punch_in_lat' => $userLat,
            'punch_in_long' => $userLng,
            'punch_in_location' => $request->location,
            'punch_in_exception' => $request->exception,
            'punch_in_image' => $imagePath,
        ]);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Punch In Success',
            'data' => $punch,
        ]);
    }

    public function punchOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'location' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        if (! empty($token)) {
            $userToken = UserToken::where('token', $token)->first();
            if (! $userToken) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token'], 401);
            }
        }

        $user = auth('api')->user();
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $today = date('Y-m-d');
        $alreadyOut = PunchIn::where('employee_id', $user->id)->whereDate('punch_out_date', $today)->first();

        if ($alreadyOut) {
            return response()->json([
                'status' => false,
                'message' => 'You have already punched out today',
                'data' => $alreadyOut,
            ]);
        }

        $userLat = $request->latitude;
        $userLng = $request->longitude;

        $locationTarget = $this->locationService->resolveLocationTarget($user);
        if (! $locationTarget['status']) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => $locationTarget['message'],
            ]);
        }

        $distance = $this->calculateDistance(
            $userLat,
            $userLng,
            $locationTarget['latitude'],
            $locationTarget['longitude']
        );

        // Distance is in KM; 0.1 KM = 100 meters.
        if ($distance > 0.1) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => $locationTarget['mismatch_message'],
                'distance_in_meters' => round($distance * 1000, 2),
            ]);
        }

        $query = PunchIn::where('employee_id', $user->id)
            ->whereDate('punch_in_date', $today)
            ->first();

        if (! $query) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => 'You have not punched in today',
            ]);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('punch_images', 'public');
        }

        $query->update([
            'punch_out_date' => $today,
            'punch_out_time' => Carbon::now(),
            'punch_out_lat' => $userLat,
            'punch_out_long' => $userLng,
            'punch_out_location' => $request->location,
            'punch_out_exception' => $request->exception,
            'punch_in_image' => $imagePath ?: $query->punch_in_image,
        ]);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Punch Out Success',
            'data' => $query->fresh(),
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // KM

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
