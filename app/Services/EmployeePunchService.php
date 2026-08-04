<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class EmployeePunchService
{
    public function __construct(private EmployeeLocationService $locationService)
    {
    }

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

        $locationCheck = $this->locationService->validatePunchCoordinates(
            $user,
            (float) $data['latitude'],
            (float) $data['longitude']
        );
        if (! ($locationCheck['status'] ?? false)) {
            if (! empty($locationCheck['mismatch_message'])) {
                return [
                    'success' => false,
                    'message' => $locationCheck['mismatch_message'],
                    'distance_in_meters' => $locationCheck['distance_in_meters'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $locationCheck['message'] ?? 'Location validation failed',
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

        $locationCheck = $this->locationService->validatePunchCoordinates(
            $user,
            (float) $data['latitude'],
            (float) $data['longitude']
        );
        if (! ($locationCheck['status'] ?? false)) {
            if (! empty($locationCheck['mismatch_message'])) {
                return [
                    'success' => false,
                    'message' => $locationCheck['mismatch_message'],
                    'distance_in_meters' => $locationCheck['distance_in_meters'] ?? null,
                ];
            }

            return [
                'success' => false,
                'message' => $locationCheck['message'] ?? 'Location validation failed',
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
}
