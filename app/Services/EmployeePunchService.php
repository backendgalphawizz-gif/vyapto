<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeePunchService
{
    public function todayAttendance(User $user): ?Attendance
    {
        return Attendance::where('employee_id', $user->id)
            ->whereDate('punch_in_date', today())
            ->first();
    }

    public function punchIn(User $user, array $data, ?UploadedFile $image = null): array
    {
        date_default_timezone_set('Asia/Kolkata');

        if ($this->todayAttendance($user)) {
            return [
                'success' => false,
                'message' => 'You have already punched in today.',
            ];
        }

        $locationTarget = $this->getLocationTargetForUser($user);
        if (! $locationTarget['status']) {
            return [
                'success' => false,
                'message' => $locationTarget['message'],
            ];
        }

        $distance = $this->calculateDistance(
            (float) $data['latitude'],
            (float) $data['longitude'],
            (float) $locationTarget['latitude'],
            (float) $locationTarget['longitude']
        );

        if ($distance > 0.1) {
            return [
                'success' => false,
                'message' => $locationTarget['mismatch_message'],
                'distance_in_meters' => round($distance * 1000, 2),
            ];
        }

        $imagePath = null;
        if ($image) {
            $imagePath = $image->store('punch_images', 'public');
        }

        $punch = Attendance::create([
            'employee_id' => $user->id,
            'punch_in_date' => date('Y-m-d'),
            'punch_in_time' => Carbon::now(),
            'punch_in_lat' => $data['latitude'],
            'punch_in_long' => $data['longitude'],
            'punch_in_location' => $data['location'],
            'punch_in_exception' => $data['exception'] ?? null,
            'punch_in_image' => $imagePath,
        ]);

        return [
            'success' => true,
            'message' => 'Punch in successful.',
            'data' => $punch,
        ];
    }

    public function punchOut(User $user, array $data, ?UploadedFile $image = null): array
    {
        date_default_timezone_set('Asia/Kolkata');

        $attendance = $this->todayAttendance($user);

        if (! $attendance) {
            return [
                'success' => false,
                'message' => 'Please punch in first.',
            ];
        }

        if ($attendance->punch_out_time) {
            return [
                'success' => false,
                'message' => 'You have already punched out today.',
            ];
        }

        $locationTarget = $this->getLocationTargetForUser($user);
        if (! $locationTarget['status']) {
            return [
                'success' => false,
                'message' => $locationTarget['message'],
            ];
        }

        $distance = $this->calculateDistance(
            (float) $data['latitude'],
            (float) $data['longitude'],
            (float) $locationTarget['latitude'],
            (float) $locationTarget['longitude']
        );

        if ($distance > 0.1) {
            return [
                'success' => false,
                'message' => $locationTarget['mismatch_message'],
                'distance_in_meters' => round($distance * 1000, 2),
            ];
        }

        $imagePath = null;
        if ($image) {
            $imagePath = $image->store('punch_images', 'public');
        }

        $attendance->update([
            'punch_out_date' => date('Y-m-d'),
            'punch_out_time' => Carbon::now(),
            'punch_out_lat' => $data['latitude'],
            'punch_out_long' => $data['longitude'],
            'punch_out_location' => $data['location'],
            'punch_out_exception' => $data['exception'] ?? null,
            'punch_out_image' => $imagePath,
        ]);

        return [
            'success' => true,
            'message' => 'Punch out successful.',
            'data' => $attendance->fresh(),
        ];
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;
        $a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function getLocationTargetForUser(User $user): array
    {
        $roleId = (int) ($user->role_id ?? 0);

        if ($roleId === 3) {
            $hubCoordinates = $this->getAssignedHubCoordinates($user->id);
            if (! $hubCoordinates['status']) {
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
            if (! $companyCoordinates['status']) {
                return $companyCoordinates;
            }

            return [
                'status' => true,
                'latitude' => $companyCoordinates['latitude'],
                'longitude' => $companyCoordinates['longitude'],
                'mismatch_message' => 'Office location not matched',
            ];
        }

        return [
            'status' => false,
            'message' => 'Location validation is not configured for your role.',
        ];
    }

    private function getCompanyCoordinates(): array
    {
        $officeLat = DB::table('settings')->where('type', 'company_lat')->value('value')
            ?? DB::table('settings')->where('type', 'company_latitude')->value('value');
        $officeLng = DB::table('settings')->where('type', 'company_long')->value('value')
            ?? DB::table('settings')->where('type', 'company_longitude')->value('value');

        if ($officeLat === null || $officeLng === null) {
            return [
                'status' => false,
                'message' => 'Office location is not configured.',
            ];
        }

        return [
            'status' => true,
            'latitude' => (float) $officeLat,
            'longitude' => (float) $officeLng,
        ];
    }

    private function getAssignedHubCoordinates(int $employeeId): array
    {
        $assignmentTable = Schema::hasTable('assignment_parcel')
            ? 'assignment_parcel'
            : (Schema::hasTable('assignment_parcels') ? 'assignment_parcels' : null);

        if (! $assignmentTable) {
            return [
                'status' => false,
                'message' => 'Assignment table not found.',
            ];
        }

        $assignmentQuery = DB::table($assignmentTable)->where('user_id', $employeeId);

        if (Schema::hasColumn($assignmentTable, 'assignment_date')) {
            $assignmentQuery->orderByDesc('assignment_date');
        }
        if (Schema::hasColumn($assignmentTable, 'created_at')) {
            $assignmentQuery->orderByDesc('created_at');
        }

        $assignment = $assignmentQuery->first();

        if (! $assignment || empty($assignment->hub_id)) {
            return [
                'status' => false,
                'message' => 'No hub assigned to you.',
            ];
        }

        $hub = DB::table('hubs')
            ->where('id', $assignment->hub_id)
            ->select('id', 'name', 'latitude', 'longitude')
            ->first();

        if (! $hub || $hub->latitude === null || $hub->longitude === null) {
            return [
                'status' => false,
                'message' => 'Assigned hub location is not configured.',
            ];
        }

        return [
            'status' => true,
            'latitude' => (float) $hub->latitude,
            'longitude' => (float) $hub->longitude,
        ];
    }
}
