<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Api\PunchIn;
use App\Models\Api\UserToken;
use App\Models\Api\Setting;
use App\Models\User as AdminUser;
use App\Services\AttendanceScheduleService;
use App\Support\StaffRoles;
use App\Support\StorageAssets;
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
                'data' => $this->formatPunchForApi($alreadyPunch)
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

        $punchInTime = Carbon::now();
        $punchInTiming = $this->resolvePunchInTiming($user, $today, $punchInTime);

        $punchInException = $request->exception;
        if (($punchInTiming['timing'] ?? '') === 'late') {
            $punchInException = $punchInException ?: 'Late arrival';
        } elseif (($punchInTiming['timing'] ?? '') === 'early') {
            $punchInException = $punchInException ?: 'Early arrival';
        }

        $punch = PunchIn::create([
            'employee_id' => $user->id,
            'punch_in_date' => date('Y-m-d'),
            'punch_in_time' => $punchInTime,
            'punch_in_lat' => $userLat,
            'punch_in_long' => $userLng,
            'punch_in_location' => $request->location,
            'punch_in_exception' => $punchInException,
            'punch_in_image' => $imagePath,
        ]);


        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $punchInTiming['message'] ?? 'Punch In Success',
            'punch_in_timing' => [
                'timing' => $punchInTiming['timing'] ?? 'unknown',
                'minutes_diff' => $punchInTiming['minutes_diff'] ?? 0,
                'expected_start_time' => $punchInTiming['expected_start_time'] ?? null,
                'location_type' => $punchInTiming['location_type'] ?? null,
                'location_name' => $punchInTiming['location_name'] ?? null,
            ],
            'data' => $this->formatPunchForApi($punch),
            //'image_url' => $imageUrl
        ]);
    }


    public function punchOut(Request $request)
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
                'data' => $this->formatPunchForApi($punch)
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

        $punchOutTime = Carbon::now();
        $punchOutTiming = $this->resolvePunchOutTiming($user, $today, $punchOutTime);

        $punchOutException = $request->exception;
        if (($punchOutTiming['timing'] ?? '') === 'early') {
            $punchOutException = $punchOutException ?: 'Early leave';
        } elseif (($punchOutTiming['timing'] ?? '') === 'late') {
            $punchOutException = $punchOutException ?: 'Late punch out';
        }

        $query->update([
            'punch_out_date' => $today,
            'punch_out_time' => $punchOutTime,
            'punch_out_lat' => $userLat,
            'punch_out_long' => $userLng,
            'punch_out_location' => $request->location,
            'punch_out_exception' => $punchOutException,
            // Keep current schema usage; change to punch_out_image only if column exists.
            'punch_in_image' => $imagePath ?: $query->punch_in_image,
        ]);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $punchOutTiming['message'] ?? 'Punch Out Success',
            'punch_out_timing' => [
                'timing' => $punchOutTiming['timing'] ?? 'unknown',
                'minutes_diff' => $punchOutTiming['minutes_diff'] ?? 0,
                'expected_end_time' => $punchOutTiming['expected_end_time'] ?? null,
                'location_type' => $punchOutTiming['location_type'] ?? null,
                'location_name' => $punchOutTiming['location_name'] ?? null,
            ],
            'data' => $this->formatPunchForApi($query->fresh())
        ]);
    }

    private function resolvePunchInTiming($user, string $date, Carbon $punchInTime): array
    {
        $schedule = $this->resolvePunchSchedule($user, $date);

        return app(AttendanceScheduleService::class)
            ->evaluatePunchInTiming($punchInTime, $date, $schedule);
    }

    private function resolvePunchOutTiming($user, string $date, Carbon $punchOutTime): array
    {
        $schedule = $this->resolvePunchSchedule($user, $date);

        return app(AttendanceScheduleService::class)
            ->evaluatePunchOutTiming($punchOutTime, $date, $schedule);
    }

    /**
     * @return array{start_time:?string,end_time:?string,half_time:?string,source:?string,source_name:?string}
     */
    private function resolvePunchSchedule($user, string $date): array
    {
        /** @var AttendanceScheduleService $scheduleService */
        $scheduleService = app(AttendanceScheduleService::class);

        $schedule = [
            'start_time' => null,
            'end_time' => null,
            'half_time' => null,
            'source' => null,
            'source_name' => null,
        ];

        if (StaffRoles::isDriverRoleId($user->role_id ?? 0)) {
            $hubData = $this->getAssignedHubCoordinates($user->id);
            if ($hubData['status']) {
                $hub = DB::table('hubs')
                    ->where('id', $hubData['hub_id'])
                    ->select('name', 'opening_time', 'closing_time')
                    ->first();

                if ($hub && (! empty($hub->opening_time) || ! empty($hub->closing_time))) {
                    $schedule = [
                        'start_time' => $hub->opening_time ? Carbon::parse($hub->opening_time)->format('H:i:s') : null,
                        'end_time' => $hub->closing_time ? Carbon::parse($hub->closing_time)->format('H:i:s') : null,
                        'half_time' => null,
                        'source' => 'hub',
                        'source_name' => $hub->name,
                    ];
                }
            }
        } elseif (StaffRoles::isStaffEmployeeRoleId($user->role_id ?? 0)) {
            $officeData = $this->getAssignedOfficeCoordinates($user->id);
            if ($officeData['status']) {
                $office = DB::table('offices')
                    ->where('id', $officeData['office_id'])
                    ->select('name', 'opening_time', 'closing_time')
                    ->first();

                if ($office && (! empty($office->opening_time) || ! empty($office->closing_time))) {
                    $schedule = [
                        'start_time' => $office->opening_time ? Carbon::parse($office->opening_time)->format('H:i:s') : null,
                        'end_time' => $office->closing_time ? Carbon::parse($office->closing_time)->format('H:i:s') : null,
                        'half_time' => null,
                        'source' => 'office',
                        'source_name' => $office->name,
                    ];
                }
            }
        }

        $employee = AdminUser::query()->find($user->id);
        if ($employee) {
            $fallback = $scheduleService->resolveSchedule($employee, $date);
            if (empty($schedule['start_time'])) {
                $schedule['start_time'] = $fallback['start_time'] ?? null;
            }
            if (empty($schedule['end_time'])) {
                $schedule['end_time'] = $fallback['end_time'] ?? null;
            }
            if (empty($schedule['half_time'])) {
                $schedule['half_time'] = $fallback['half_time'] ?? null;
            }
            if (empty($schedule['source'])) {
                $schedule['source'] = $fallback['source'] ?? null;
                $schedule['source_name'] = $fallback['source_name'] ?? null;
            }
        }

        return $schedule;
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
        // Driver → Hub from latest assignment
        if (StaffRoles::isDriverRoleId($user->role_id ?? 0)) {
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

        // Staff Employee → Office from latest assignment
        if (StaffRoles::isStaffEmployeeRoleId($user->role_id ?? 0)) {
            $officeCoordinates = $this->getAssignedOfficeCoordinates($user->id);
            if (!$officeCoordinates['status']) {
                return $officeCoordinates;
            }

            return [
                'status' => true,
                'latitude' => $officeCoordinates['latitude'],
                'longitude' => $officeCoordinates['longitude'],
                'mismatch_message' => 'Assigned office location not matched',
            ];
        }

        return [
            'status' => false,
            'message' => 'Location validation is not configured for this role.',
        ];
    }

    private function assignmentTableName(): ?string
    {
        if (Schema::hasTable('assignment_parcels')) {
            return 'assignment_parcels';
        }
        if (Schema::hasTable('assignment_parcel')) {
            return 'assignment_parcel';
        }

        return null;
    }

    private function latestAssignmentForUser(int $employeeId, ?string $locationColumn = null)
    {
        $assignmentTable = $this->assignmentTableName();
        if (!$assignmentTable) {
            return null;
        }

        $today = date('Y-m-d');
        $assignmentQuery = DB::table($assignmentTable)->where('user_id', $employeeId);

        if ($locationColumn && Schema::hasColumn($assignmentTable, $locationColumn)) {
            $assignmentQuery->whereNotNull($locationColumn);
        }

        // Prefer assignments active for today (from_date / to_date)
        if (Schema::hasColumn($assignmentTable, 'from_date') || Schema::hasColumn($assignmentTable, 'to_date')) {
            $assignmentQuery->where(function ($q) use ($today, $assignmentTable) {
                if (Schema::hasColumn($assignmentTable, 'from_date')) {
                    $q->where(function ($inner) use ($today) {
                        $inner->whereNull('from_date')->orWhereDate('from_date', '<=', $today);
                    });
                }
                if (Schema::hasColumn($assignmentTable, 'to_date')) {
                    $q->where(function ($inner) use ($today) {
                        $inner->whereNull('to_date')->orWhereDate('to_date', '>=', $today);
                    });
                }
            });
        }

        if (Schema::hasColumn($assignmentTable, 'from_date')) {
            $assignmentQuery->orderByDesc('from_date');
        }
        if (Schema::hasColumn($assignmentTable, 'assignment_date')) {
            $assignmentQuery->orderByDesc('assignment_date');
        }
        if (Schema::hasColumn($assignmentTable, 'created_at')) {
            $assignmentQuery->orderByDesc('created_at');
        }

        $active = $assignmentQuery->first();
        if ($active) {
            return $active;
        }

        // Fallback: latest assignment even if date range columns are missing/expired
        $fallback = DB::table($assignmentTable)->where('user_id', $employeeId);
        if ($locationColumn && Schema::hasColumn($assignmentTable, $locationColumn)) {
            $fallback->whereNotNull($locationColumn);
        }
        if (Schema::hasColumn($assignmentTable, 'assignment_date')) {
            $fallback->orderByDesc('assignment_date');
        }
        if (Schema::hasColumn($assignmentTable, 'created_at')) {
            $fallback->orderByDesc('created_at');
        }

        return $fallback->first();
    }

    private function getAssignedOfficeCoordinates($employeeId)
    {
        $assignmentTable = $this->assignmentTableName();
        if (!$assignmentTable) {
            return [
                'status' => false,
                'message' => 'Assignment table not found.',
            ];
        }

        if (! Schema::hasColumn($assignmentTable, 'office_id')) {
            return [
                'status' => false,
                'message' => 'Office assignment is not available. Run migrations first.',
            ];
        }

        $assignment = $this->latestAssignmentForUser((int) $employeeId, 'office_id');

        if (!$assignment || empty($assignment->office_id)) {
            return [
                'status' => false,
                'message' => 'No active office assignment for today. Assign this staff to an Office with From/To dates.',
            ];
        }

        // If dates exist, enforce active range strictly
        $today = date('Y-m-d');
        if (!empty($assignment->from_date) && $today < $assignment->from_date) {
            return [
                'status' => false,
                'message' => 'Office assignment has not started yet (from ' . $assignment->from_date . ').',
            ];
        }
        if (!empty($assignment->to_date) && $today > $assignment->to_date) {
            return [
                'status' => false,
                'message' => 'Office assignment ended on ' . $assignment->to_date . '.',
            ];
        }

        if (! Schema::hasTable('offices')) {
            return [
                'status' => false,
                'message' => 'Offices table not found.',
            ];
        }

        $office = DB::table('offices')
            ->where('id', $assignment->office_id)
            ->select('id', 'name', 'latitude', 'longitude')
            ->first();

        if (!$office) {
            return [
                'status' => false,
                'message' => 'Assigned office not found.',
            ];
        }

        if ($office->latitude === null || $office->longitude === null || $office->latitude === '' || $office->longitude === '') {
            return [
                'status' => false,
                'message' => 'Assigned office location is not configured. Add latitude/longitude on that Office.',
            ];
        }

        return [
            'status' => true,
            'office_id' => $office->id,
            'office_name' => $office->name,
            'latitude' => (float) $office->latitude,
            'longitude' => (float) $office->longitude,
        ];
    }

    private function getAssignedHubCoordinates($employeeId)
    {
        $assignmentTable = $this->assignmentTableName();
        if (!$assignmentTable) {
            return [
                'status' => false,
                'message' => 'Assignment table not found.',
            ];
        }

        $assignment = $this->latestAssignmentForUser((int) $employeeId, 'hub_id');

        if (!$assignment || empty($assignment->hub_id)) {
            return [
                'status' => false,
                'message' => 'No hub assigned to this employee. Assign them to a Hub first.',
            ];
        }

        $hub = DB::table('hubs')
            ->where('id', $assignment->hub_id)
            ->select('id', 'name', 'latitude', 'longitude')
            ->first();

        if (!$hub) {
            return [
                'status' => false,
                'message' => 'Assigned hub not found.',
            ];
        }

        if ($hub->latitude === null || $hub->longitude === null || $hub->latitude === '' || $hub->longitude === '') {
            return [
                'status' => false,
                'message' => 'Assigned hub location is not configured. Add latitude/longitude on that Hub.',
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

    private function formatPunchForApi(PunchIn $punch): array
    {
        $data = $punch->toArray();

        if (! empty($data['punch_in_image'])) {
            $data['punch_in_image'] = StorageAssets::publicUrl($data['punch_in_image'], $data['punch_in_image']);
        }

        if (! empty($data['punch_out_image'])) {
            $data['punch_out_image'] = StorageAssets::publicUrl($data['punch_out_image'], $data['punch_out_image']);
        }

        return $data;
    }
}
