<?php

namespace App\Services;

use App\Models\Api\User as ApiUser;
use App\Models\User;
use App\Support\StaffRoles;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeLocationService
{
    /**
     * Resolve punch geofence target:
     * 1) Active assignment parcel hub/office (if any)
     * 2) Fallback to creation-time users.hub_id / users.office_id
     */
    public function resolveLocationTarget(User|ApiUser $user): array
    {
        $roleId = (int) ($user->role_id ?? 0);
        $userId = (int) ($user->id ?? 0);

        if ($userId <= 0) {
            return [
                'status' => false,
                'message' => 'Employee not found.',
            ];
        }

        if (StaffRoles::isDriverRoleId($roleId)) {
            $hubCoordinates = $this->resolveHubCoordinates($userId);
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

        if (StaffRoles::isStaffEmployeeRoleId($roleId)) {
            $officeCoordinates = $this->resolveOfficeCoordinates($userId);
            if (! $officeCoordinates['status']) {
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

    public function resolveHubCoordinates(int $employeeId): array
    {
        $assignment = $this->latestAssignmentForUser($employeeId, 'hub_id');
        if ($assignment && ! empty($assignment->hub_id)) {
            return $this->hubCoordinatesFromId((int) $assignment->hub_id);
        }

        $user = User::query()->find($employeeId);
        if ($user && ! empty($user->hub_id)) {
            return $this->hubCoordinatesFromId((int) $user->hub_id);
        }

        return [
            'status' => false,
            'message' => 'No hub assigned to this employee. Assign a Hub on the employee profile.',
        ];
    }

    public function resolveOfficeCoordinates(int $employeeId): array
    {
        $assignment = $this->latestAssignmentForUser($employeeId, 'office_id');
        if ($assignment && ! empty($assignment->office_id)) {
            $today = date('Y-m-d');
            if (! empty($assignment->from_date) && $today < $assignment->from_date) {
                return [
                    'status' => false,
                    'message' => 'Office assignment has not started yet (from '.$assignment->from_date.').',
                ];
            }
            if (! empty($assignment->to_date) && $today > $assignment->to_date) {
                return [
                    'status' => false,
                    'message' => 'Office assignment ended on '.$assignment->to_date.'.',
                ];
            }

            return $this->officeCoordinatesFromId((int) $assignment->office_id);
        }

        $user = User::query()->find($employeeId);
        if ($user && ! empty($user->office_id)) {
            $dateCheck = $this->validateProfileLocationDates($employeeId);
            if (! $dateCheck['status']) {
                return $dateCheck;
            }

            return $this->officeCoordinatesFromId((int) $user->office_id);
        }

        return [
            'status' => false,
            'message' => 'No office assigned to this employee. Assign an Office with From/To dates on the employee profile.',
        ];
    }

    public function locationActiveForDate(User $user, string $date): bool
    {
        return $this->isProfileLocationActiveForDate((int) $user->id, $date);
    }

    private function validateProfileLocationDates(int $employeeId): array
    {
        if (! Schema::hasColumn('users', 'location_from_date') && ! Schema::hasColumn('users', 'location_to_date')) {
            return ['status' => true];
        }

        $user = User::query()->find($employeeId);
        if (! $user) {
            return ['status' => true];
        }

        $today = date('Y-m-d');
        $fromDate = $this->normalizeDateValue($user->location_from_date ?? null);
        $toDate = $this->normalizeDateValue($user->location_to_date ?? null);

        if ($fromDate && $today < $fromDate) {
            return [
                'status' => false,
                'message' => 'Office assignment has not started yet (from '.$fromDate.').',
            ];
        }

        if ($toDate && $today > $toDate) {
            return [
                'status' => false,
                'message' => 'Office assignment ended on '.$toDate.'.',
            ];
        }

        return ['status' => true];
    }

    private function isProfileLocationActiveForDate(int $employeeId, string $date): bool
    {
        if (! Schema::hasColumn('users', 'location_from_date') && ! Schema::hasColumn('users', 'location_to_date')) {
            return true;
        }

        $user = User::query()->find($employeeId);
        if (! $user) {
            return true;
        }

        $fromDate = $this->normalizeDateValue($user->location_from_date ?? null);
        $toDate = $this->normalizeDateValue($user->location_to_date ?? null);

        if ($fromDate && $date < $fromDate) {
            return false;
        }

        if ($toDate && $date > $toDate) {
            return false;
        }

        return true;
    }

    private function normalizeDateValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->format('Y-m-d');
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function hubCoordinatesFromId(int $hubId): array
    {
        if (! Schema::hasTable('hubs')) {
            return [
                'status' => false,
                'message' => 'Hubs table not found.',
            ];
        }

        $hub = DB::table('hubs')
            ->where('id', $hubId)
            ->select('id', 'name', 'latitude', 'longitude')
            ->first();

        if (! $hub) {
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

    private function officeCoordinatesFromId(int $officeId): array
    {
        if (! Schema::hasTable('offices')) {
            return [
                'status' => false,
                'message' => 'Offices table not found.',
            ];
        }

        $office = DB::table('offices')
            ->where('id', $officeId)
            ->select('id', 'name', 'latitude', 'longitude')
            ->first();

        if (! $office) {
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

    private function latestAssignmentForUser(int $employeeId, ?string $locationColumn = null): ?object
    {
        $assignmentTable = $this->assignmentTableName();
        if (! $assignmentTable) {
            return null;
        }

        $today = date('Y-m-d');
        $assignmentQuery = DB::table($assignmentTable)->where('user_id', $employeeId);

        if ($locationColumn && Schema::hasColumn($assignmentTable, $locationColumn)) {
            $assignmentQuery->whereNotNull($locationColumn);
        }

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

        return null;
    }
}
