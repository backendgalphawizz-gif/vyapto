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
     * Resolve punch geofence target (single — used for schedule/timing messages).
     * Drivers → hub. Staff → office first, then hub.
     *
     * For punch distance checks use validatePunchCoordinates() so staff can
     * punch at either assigned office or hub.
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
            $locations = $this->resolveAllowedLocations($user);
            if ($locations === []) {
                return [
                    'status' => false,
                    'message' => 'No office/hub assigned to this employee. Assign an Office (and optional Hub) on the employee profile.',
                ];
            }

            $primary = $locations[0];

            return [
                'status' => true,
                'latitude' => $primary['latitude'],
                'longitude' => $primary['longitude'],
                'mismatch_message' => 'Assigned office/hub location not matched',
            ];
        }

        return [
            'status' => false,
            'message' => 'Location validation is not configured for this role.',
        ];
    }

    /**
     * Validate punch GPS against all allowed locations (within $maxKm).
     * Staff may match office OR hub. Drivers match hub only.
     *
     * @return array{status:bool,message?:string,mismatch_message?:string,distance_in_meters?:float,matched?:array}
     */
    public function validatePunchCoordinates(User|ApiUser $user, float $latitude, float $longitude, float $maxKm = 0.1): array
    {
        $locations = $this->resolveAllowedLocations($user);

        if ($locations === []) {
            $roleId = (int) ($user->role_id ?? 0);
            if (StaffRoles::isDriverRoleId($roleId)) {
                return [
                    'status' => false,
                    'message' => 'No hub assigned to this employee. Assign a Hub on the employee profile.',
                ];
            }

            return [
                'status' => false,
                'message' => 'No office/hub assigned to this employee. Assign an Office (and optional Hub) on the employee profile.',
            ];
        }

        $bestDistance = null;

        foreach ($locations as $location) {
            $distance = $this->haversineKm(
                $latitude,
                $longitude,
                (float) $location['latitude'],
                (float) $location['longitude']
            );

            if ($bestDistance === null || $distance < $bestDistance) {
                $bestDistance = $distance;
            }

            if ($distance <= $maxKm) {
                return [
                    'status' => true,
                    'matched' => $location,
                    'distance_in_meters' => round($distance * 1000, 2),
                ];
            }
        }

        return [
            'status' => false,
            'mismatch_message' => 'Assigned office/hub location not matched',
            'distance_in_meters' => round(($bestDistance ?? 0) * 1000, 2),
        ];
    }

    /**
     * @return list<array{type:string,id:int,name:?string,latitude:float,longitude:float}>
     */
    public function resolveAllowedLocations(User|ApiUser $user): array
    {
        $roleId = (int) ($user->role_id ?? 0);
        $userId = (int) ($user->id ?? 0);
        $locations = [];
        $seen = [];

        $push = function (array $coords, string $type) use (&$locations, &$seen): void {
            if (! ($coords['status'] ?? false)) {
                return;
            }
            $key = $type.':'.($coords[$type.'_id'] ?? $coords['latitude'].','.$coords['longitude']);
            if (isset($seen[$key])) {
                return;
            }
            $seen[$key] = true;
            $locations[] = [
                'type' => $type,
                'id' => (int) ($coords[$type.'_id'] ?? 0),
                'name' => $coords[$type.'_name'] ?? null,
                'latitude' => (float) $coords['latitude'],
                'longitude' => (float) $coords['longitude'],
            ];
        };

        if (StaffRoles::isDriverRoleId($roleId)) {
            $push($this->resolveHubCoordinates($userId), 'hub');

            return $locations;
        }

        if (StaffRoles::isStaffEmployeeRoleId($roleId)) {
            // Office first (primary), then optional hub — punch allowed at either.
            $push($this->resolveOfficeCoordinates($userId), 'office');
            $push($this->resolveProfileHubCoordinates($userId), 'hub');

            return $locations;
        }

        return $locations;
    }

    /**
     * Staff profile hub only (does not require driver role).
     */
    public function resolveProfileHubCoordinates(int $employeeId): array
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
            'message' => 'No hub assigned.',
        ];
    }

    public function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
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

    /**
     * When a new assignment is given, overwrite the employee's profile location
     * and close older overlapping location assignments so punches use the new place.
     */
    public function applyAssignmentLocation(object $assignment): void
    {
        $userId = (int) ($assignment->user_id ?? 0);
        if ($userId <= 0) {
            return;
        }

        $hubId = ! empty($assignment->hub_id) ? (int) $assignment->hub_id : null;
        $officeId = ! empty($assignment->office_id) ? (int) $assignment->office_id : null;
        $fromDate = $this->normalizeDateValue($assignment->from_date ?? $assignment->assignment_date ?? null);
        $toDate = $this->normalizeDateValue($assignment->to_date ?? null);
        $assignmentId = (int) ($assignment->id ?? 0);

        $this->closeOlderLocationAssignments($userId, $assignmentId, $fromDate);

        $user = User::query()->find($userId);
        if (! $user) {
            return;
        }

        $updates = [];

        if ($hubId && ! $officeId) {
            // Driver / hub-only assignment
            $updates['hub_id'] = $hubId;
            $updates['office_id'] = null;
            if (Schema::hasColumn('users', 'location_from_date')) {
                $updates['location_from_date'] = null;
            }
            if (Schema::hasColumn('users', 'location_to_date')) {
                $updates['location_to_date'] = null;
            }
        } elseif ($officeId) {
            // Staff office assignment — keep any profile hub so they can punch at both
            $updates['office_id'] = $officeId;
            if (Schema::hasColumn('users', 'location_from_date')) {
                $updates['location_from_date'] = $fromDate;
            }
            if (Schema::hasColumn('users', 'location_to_date')) {
                $updates['location_to_date'] = $toDate;
            }
            if ($hubId) {
                $updates['hub_id'] = $hubId;
            }
        } elseif ($hubId) {
            $updates['hub_id'] = $hubId;
        }

        if ($updates !== []) {
            $user->forceFill($updates)->save();
        }
    }

    /**
     * When admin edits employee hub/office on profile, keep punch location in sync
     * by updating the newest active assignment (or closing old ones).
     */
    public function applyProfileLocation(User $user): void
    {
        $userId = (int) $user->id;
        $assignmentTable = $this->assignmentTableName();
        if (! $assignmentTable) {
            return;
        }

        $today = date('Y-m-d');
        $latest = DB::table($assignmentTable)
            ->where('user_id', $userId)
            ->where(function ($q) use ($assignmentTable) {
                if (Schema::hasColumn($assignmentTable, 'hub_id')) {
                    $q->whereNotNull('hub_id');
                }
                if (Schema::hasColumn($assignmentTable, 'office_id')) {
                    $q->orWhereNotNull('office_id');
                }
            })
            ->orderByDesc('id')
            ->first();

        if (! $latest) {
            return;
        }

        $fromDate = $this->normalizeDateValue($user->location_from_date ?? null) ?: $today;
        $toDate = $this->normalizeDateValue($user->location_to_date ?? null);

        $this->closeOlderLocationAssignments($userId, (int) $latest->id, $fromDate);

        $payload = [];
        $isStaff = StaffRoles::isStaffEmployeeRoleId($user->role_id ?? 0);

        if ($isStaff) {
            if (! empty($user->office_id) && Schema::hasColumn($assignmentTable, 'office_id')) {
                $payload['office_id'] = (int) $user->office_id;
            }
            if (Schema::hasColumn($assignmentTable, 'hub_id')) {
                $payload['hub_id'] = ! empty($user->hub_id) ? (int) $user->hub_id : null;
            }
            if (Schema::hasColumn($assignmentTable, 'from_date')) {
                $payload['from_date'] = $fromDate;
            }
            if (Schema::hasColumn($assignmentTable, 'to_date')) {
                $payload['to_date'] = $toDate;
            }
            if (Schema::hasColumn($assignmentTable, 'assignment_date')) {
                $payload['assignment_date'] = $fromDate;
            }
        } elseif (! empty($user->hub_id) && Schema::hasColumn($assignmentTable, 'hub_id')) {
            $payload['hub_id'] = (int) $user->hub_id;
            if (Schema::hasColumn($assignmentTable, 'office_id')) {
                $payload['office_id'] = null;
            }
            if (Schema::hasColumn($assignmentTable, 'from_date')) {
                $payload['from_date'] = null;
            }
            if (Schema::hasColumn($assignmentTable, 'to_date')) {
                $payload['to_date'] = null;
            }
            if (Schema::hasColumn($assignmentTable, 'assignment_date')) {
                $payload['assignment_date'] = $today;
            }
        } elseif (! empty($user->office_id) && Schema::hasColumn($assignmentTable, 'office_id')) {
            $payload['office_id'] = (int) $user->office_id;
            if (Schema::hasColumn($assignmentTable, 'hub_id')) {
                $payload['hub_id'] = null;
            }
            if (Schema::hasColumn($assignmentTable, 'from_date')) {
                $payload['from_date'] = $fromDate;
            }
            if (Schema::hasColumn($assignmentTable, 'to_date')) {
                $payload['to_date'] = $toDate;
            }
            if (Schema::hasColumn($assignmentTable, 'assignment_date')) {
                $payload['assignment_date'] = $fromDate;
            }
        }

        if ($payload !== []) {
            DB::table($assignmentTable)->where('id', $latest->id)->update($payload);
        }
    }

    private function closeOlderLocationAssignments(int $userId, int $keepAssignmentId, ?string $newFromDate): void
    {
        $assignmentTable = $this->assignmentTableName();
        if (! $assignmentTable || $keepAssignmentId <= 0) {
            return;
        }

        $endBefore = $newFromDate
            ? Carbon::parse($newFromDate)->subDay()->format('Y-m-d')
            : Carbon::yesterday()->format('Y-m-d');

        $query = DB::table($assignmentTable)
            ->where('user_id', $userId)
            ->where('id', '!=', $keepAssignmentId);

        if (Schema::hasColumn($assignmentTable, 'hub_id') || Schema::hasColumn($assignmentTable, 'office_id')) {
            $query->where(function ($q) use ($assignmentTable) {
                if (Schema::hasColumn($assignmentTable, 'hub_id')) {
                    $q->whereNotNull('hub_id');
                }
                if (Schema::hasColumn($assignmentTable, 'office_id')) {
                    $q->orWhereNotNull('office_id');
                }
            });
        }

        // End open / future-overlapping older assignments so only the new location is punchable.
        if (Schema::hasColumn($assignmentTable, 'to_date')) {
            $query->where(function ($q) use ($endBefore) {
                $q->whereNull('to_date')->orWhereDate('to_date', '>=', $endBefore);
            });
            $query->update(['to_date' => $endBefore]);
        }
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

        // Newest assignment always wins (new location replaces the old one).
        $assignmentQuery->orderByDesc('id');

        return $assignmentQuery->first();
    }
}
