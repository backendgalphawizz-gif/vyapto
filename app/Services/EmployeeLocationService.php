<?php

namespace App\Services;

use App\Models\User;
use App\Support\StaffRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmployeeLocationService
{
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
            $dateCheck = $this->validateProfileLocationDates($user);
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
        if (! $user->location_from_date && ! $user->location_to_date) {
            return true;
        }

        if ($user->location_from_date && $date < $user->location_from_date->format('Y-m-d')) {
            return false;
        }

        if ($user->location_to_date && $date > $user->location_to_date->format('Y-m-d')) {
            return false;
        }

        return true;
    }

    public function resolveLocationTarget(User $user): array
    {
        if (StaffRoles::isDriverRoleId($user->role_id ?? 0)) {
            $hubCoordinates = $this->resolveHubCoordinates((int) $user->id);
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

        if (StaffRoles::isStaffEmployeeRoleId($user->role_id ?? 0)) {
            $officeCoordinates = $this->resolveOfficeCoordinates((int) $user->id);
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

    private function validateProfileLocationDates(User $user): array
    {
        $today = date('Y-m-d');

        if ($user->location_from_date && $today < $user->location_from_date->format('Y-m-d')) {
            return [
                'status' => false,
                'message' => 'Office assignment has not started yet (from '.$user->location_from_date->format('Y-m-d').').',
            ];
        }

        if ($user->location_to_date && $today > $user->location_to_date->format('Y-m-d')) {
            return [
                'status' => false,
                'message' => 'Office assignment ended on '.$user->location_to_date->format('Y-m-d').'.',
            ];
        }

        return ['status' => true];
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
}
