<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

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

        $locationTarget = app(EmployeeLocationService::class)->resolveLocationTarget($user);
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

        $locationTarget = app(EmployeeLocationService::class)->resolveLocationTarget($user);
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
}
