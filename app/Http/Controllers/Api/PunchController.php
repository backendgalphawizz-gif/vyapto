<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Api\PunchIn;
use App\Models\Api\UserToken;
use App\Models\Api\Setting;
use Carbon\Carbon;
use Validator;
use Auth;
use DB;
use Illuminate\Support\Facades\Schema;

class PunchController extends Controller
{
    public function punchIn(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'location' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        if (!empty($token)) {
            $userToken = UserToken::where('token', $token)->first();
            if (!$userToken) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token'], 401);
            }
        }

        $user = auth('api')->user();
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('punch_images', 'public'); // storage/app/public/punch_images
        }

        $today = date('Y-m-d');
        $alreadyPunch = PunchIn::where('employee_id', $user->id)->whereDate('punch_in_date', $today)->first();

        if ($alreadyPunch) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => 'You have already punched in today',
                'data' => $alreadyPunch
            ]);
        }

        $userLat = $request->latitude;
        $userLng = $request->longitude;

        $locationTarget = $this->getLocationTargetForUser($user);
        if (!$locationTarget['status']) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => $locationTarget['message']
            ]);
        }

        $targetLat = $locationTarget['latitude'];
        $targetLng = $locationTarget['longitude'];

        // $userLat   = (string)$userLat;
        // $userLng   = (string)$userLng;
        // $officeLat = (string)$officeLat;
        // $officeLng = (string)$officeLng;

        // // dd($userLat, $userLng, $officeLat, $officeLng);

        // if ($userLat !== $officeLat || $userLng !== $officeLng) {
        //     return response()->json([
        //         'status' => false,
        // 		'code' => 200,
        //         'message' => 'Office Location Not Matched',
        //         'userLat' => $userLat,
        //         'officeLat' => $officeLat
        //     ]);
        // }

        $distance = $this->calculateDistance($userLat, $userLng, $targetLat, $targetLng);

        // Distance is in KM; 0.1 KM = 100 meters.
        if ($distance > 0.1) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => $locationTarget['mismatch_message'],
                'distance_in_meters' => round($distance * 1000, 2)
            ]);
        }

        $imagePath = null;
        $imageUrl  = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('punch_images', 'public');
            $imageUrl  = asset('storage/' . $imagePath);
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
            //'image_url' => $imageUrl
        ]);
    }


    public function punchOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'location' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }



        $token = str_replace('Bearer ', '', $request->header('Authorization'));
        if (!empty($token)) {
            $userToken = UserToken::where('token', $token)->first();
            if (!$userToken) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token'], 401);
            }
        }

        $user = auth('api')->user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $today = date('Y-m-d');
        $punch  = PunchIn::where('employee_id', $user->id)->whereDate('punch_out_date', $today)->first();


        if ($punch) {
            return response()->json([
                'status' => false,
                'message' => 'You have already punched out today',
                'data' => $punch
            ]);
        }
        $userLat = $request->latitude;
        $userLng = $request->longitude;

        $locationTarget = $this->getLocationTargetForUser($user);
        if (!$locationTarget['status']) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => $locationTarget['message']
            ]);
        }

        $targetLat = $locationTarget['latitude'];
        $targetLng = $locationTarget['longitude'];

        $distance = $this->calculateDistance($userLat, $userLng, $targetLat, $targetLng);

        // Distance is in KM; 0.1 KM = 100 meters.
        if ($distance > 0.1) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => $locationTarget['mismatch_message'],
                'distance_in_meters' => round($distance * 1000, 2)
            ]);
        }

        $query = PunchIn::where('employee_id', $user->id)
            ->whereDate('punch_in_date', $today)
            ->first();

        if (!$query) {
            return response()->json([
                'status' => false,
                'code' => 200,
                'message' => 'You have not punched in today'
            ]);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('punch_images', 'public'); // storage/app/public/punch_images
        }

        $query->update([
            'punch_out_date' => $today,
            'punch_out_time' => Carbon::now(),
            'punch_out_lat' => $userLat,
            'punch_out_long' => $userLng,
            'punch_out_location' => $request->location,
            'punch_out_exception' => $request->exception,
            // Keep current schema usage; change to punch_out_image only if column exists.
            'punch_in_image' => $imagePath ?: $query->punch_in_image,
        ]);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Punch Out Success',
            'data' => $query->fresh()
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

    private function getLocationTargetForUser($user)
    {
        $roleId = (int) ($user->role_id ?? 0);

        if ($roleId === 3) {
            $hubCoordinates = $this->getAssignedHubCoordinates($user->id);
            if (!$hubCoordinates['status']) {
                return $hubCoordinates;
            }

            return [
                'status' => true,
                'latitude' => $hubCoordinates['latitude'],
                'longitude' => $hubCoordinates['longitude'],
                'mismatch_message' => 'Assigned hub location not matched',
            ];
        }

        if ($roleId === 4) {

            
            $companyCoordinates = $this->getCompanyCoordinates();
            if (!$companyCoordinates['status']) {
                return $companyCoordinates;
            }

            return [
                'status' => true,
                'latitude' => $companyCoordinates['latitude'],
                'longitude' => $companyCoordinates['longitude'],
                'mismatch_message' => 'Office Location Not Matched',
            ];
        }

        return [
            'status' => false,
            'message' => 'Location validation is not configured for this role.'
        ];
    }

    private function getCompanyCoordinates()
    {
        $officeLat = DB::table('settings')->where('type', 'company_lat')->value('value');
        $officeLng = DB::table('settings')->where('type', 'company_long')->value('value');

        if ($officeLat === null || $officeLng === null) {
            return [
                'status' => false,
                'message' => 'Office location is not configured.'
            ];
        }

        return [
            'status' => true,
            'latitude' => (float) $officeLat,
            'longitude' => (float) $officeLng,
        ];
    }

    private function getAssignedHubCoordinates($employeeId)
    {
        $assignmentTable = null;
        if (Schema::hasTable('assignment_parcels')) {
            $assignmentTable = 'assignment_parcels';
        } elseif (Schema::hasTable('assignment_parcel')) {
            $assignmentTable = 'assignment_parcel';
        }

        if (!$assignmentTable) {
            return [
                'status' => false,
                'message' => 'Assignment table not found.'
            ];
        }

        $assignmentQuery = DB::table($assignmentTable)
            ->where('user_id', $employeeId);

        if (Schema::hasColumn($assignmentTable, 'assignment_date')) {
            $assignmentQuery->orderByDesc('assignment_date');
        }

        if (Schema::hasColumn($assignmentTable, 'created_at')) {
            $assignmentQuery->orderByDesc('created_at');
        }

        $assignment = $assignmentQuery->first();

        if (!$assignment || empty($assignment->hub_id)) {
            return [
                'status' => false,
                'message' => 'No hub assigned to this employee.'
            ];
        }

        $hub = DB::table('hubs')
            ->where('id', $assignment->hub_id)
            ->select('id', 'name', 'latitude', 'longitude')
            ->first();

        if (!$hub) {
            return [
                'status' => false,
                'message' => 'Assigned hub not found.'
            ];
        }

        if ($hub->latitude === null || $hub->longitude === null) {
            return [
                'status' => false,
                'message' => 'Assigned hub location is not configured.'
            ];
        }

        return [
            'status' => true,
            'hub_id' => $hub->id,
            'hub_name' => $hub->name,
            'latitude' => (float) $hub->latitude,
            'longitude' => (float) $hub->longitude,
        ];
    }
}
