<?php

namespace App\Services;

use App\Models\AssignmentParcel;
use App\Models\Attendance;
use App\Models\ParcelDetail;
use App\Models\User;
use App\Models\VehicleUsage;
use Carbon\Carbon;

class EmployeeRideService
{
    public function todayUsageCount(User $user): int
    {
        return VehicleUsage::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->count();
    }

    public function isRideActive(User $user): bool
    {
        return ($this->todayUsageCount($user) % 2) === 1;
    }

    public function rideStatus(User $user, ?Attendance $attendance = null): string
    {
        if (! $this->hasPunchedInToday($attendance)) {
            return 'not_punched_in';
        }

        if ($this->hasPunchedOutToday($attendance)) {
            return 'shift_completed';
        }

        return $this->isRideActive($user) ? 'in_progress' : 'not_started';
    }

    public function rideStatusLabel(User $user, ?Attendance $attendance = null): string
    {
        return match ($this->rideStatus($user, $attendance)) {
            'in_progress' => 'In Progress',
            'shift_completed' => 'Shift Completed',
            'not_punched_in' => 'Punch In Required',
            default => 'Not Started',
        };
    }

    public function canStartRide(User $user, ?Attendance $attendance = null): bool
    {
        return $this->hasPunchedInToday($attendance)
            && ! $this->hasPunchedOutToday($attendance)
            && ! $this->isRideActive($user);
    }

    public function canEndRide(User $user, ?Attendance $attendance = null): bool
    {
        return $this->hasPunchedInToday($attendance)
            && ! $this->hasPunchedOutToday($attendance)
            && $this->isRideActive($user);
    }

    public function canViewShipments(User $user, ?Attendance $attendance = null): bool
    {
        return $this->isRideActive($user)
            && $this->hasPunchedInToday($attendance)
            && ! $this->hasPunchedOutToday($attendance);
    }

    public function nextRideAction(User $user): string
    {
        return ($this->todayUsageCount($user) % 2 === 0) ? 'start' : 'end';
    }

    public function currentRideStartEntry(User $user): ?VehicleUsage
    {
        if (! $this->isRideActive($user)) {
            return null;
        }

        return VehicleUsage::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('id')
            ->first();
    }

    public function validateEndRideKm(User $user, float $endKm): ?string
    {
        $startEntry = $this->currentRideStartEntry($user);

        if (! $startEntry) {
            return 'Please start a ride before ending it.';
        }

        if ($endKm <= (float) $startEntry->kms) {
            return 'End ride KM must be greater than start ride KM ('.$startEntry->kms.').';
        }

        return null;
    }

    public function suggestedVehicleNumber(User $user, ?string $rideAction = null): ?string
    {
        $rideAction ??= $this->nextRideAction($user);

        if ($rideAction === 'end') {
            return $this->currentRideStartEntry($user)?->vehicle_number
                ?? $this->resolveAssignedVehicleNumber($user);
        }

        return $this->resolveAssignedVehicleNumber($user);
    }

    public function validateEndRideVehicleNumber(User $user, string $vehicleNumber): ?string
    {
        $startEntry = $this->currentRideStartEntry($user);

        if (! $startEntry) {
            return 'Please start a ride before ending it.';
        }

        if (strcasecmp(trim($vehicleNumber), trim((string) $startEntry->vehicle_number)) !== 0) {
            return 'Vehicle number must match the vehicle used when starting the ride ('.$startEntry->vehicle_number.').';
        }

        return null;
    }

    private function resolveAssignedVehicleNumber(User $user): ?string
    {
        $todayAssignment = AssignmentParcel::query()
            ->where('user_id', $user->id)
            ->whereNotNull('vehicle_id')
            ->whereDate('assignment_date', Carbon::today())
            ->with('vehicle:id,vehicle_number')
            ->orderByDesc('id')
            ->first();

        if ($todayAssignment?->vehicle?->vehicle_number) {
            return $todayAssignment->vehicle->vehicle_number;
        }

        $todayParcel = ParcelDetail::query()
            ->where('user_id', $user->id)
            ->whereNotNull('assignment_parcel_id')
            ->whereDate('created_at', Carbon::today())
            ->with('assignmentParcel.vehicle:id,vehicle_number')
            ->orderByDesc('id')
            ->first();

        if ($todayParcel?->assignmentParcel?->vehicle?->vehicle_number) {
            return $todayParcel->assignmentParcel->vehicle->vehicle_number;
        }

        $latestAssignment = AssignmentParcel::query()
            ->where('user_id', $user->id)
            ->whereNotNull('vehicle_id')
            ->with('vehicle:id,vehicle_number')
            ->orderByDesc('assignment_date')
            ->orderByDesc('id')
            ->first();

        if ($latestAssignment?->vehicle?->vehicle_number) {
            return $latestAssignment->vehicle->vehicle_number;
        }

        return VehicleUsage::where('user_id', $user->id)
            ->orderByDesc('id')
            ->value('vehicle_number');
    }

    private function hasPunchedInToday(?Attendance $attendance): bool
    {
        return $attendance && $attendance->punch_in_time;
    }

    private function hasPunchedOutToday(?Attendance $attendance): bool
    {
        return $attendance && $attendance->punch_out_time;
    }
}
