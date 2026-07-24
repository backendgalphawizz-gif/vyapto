<?php

namespace App\Support;

use Spatie\Permission\Models\Role;

class StaffRoles
{
    /**
     * Role IDs used for operational staff (assignments, vehicle usage, etc.).
     * Resolved by name so environments with different IDs still work.
     */
    public static function assignableIds(): array
    {
        $ids = Role::query()
            ->where(function ($q) {
                $q->where('name', 'Staff Employee')
                    ->orWhere('name', 'like', '%Driver%');
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($ids === []) {
            $ids = [3, 4];
        }

        return array_values(array_unique($ids));
    }

    public static function staffEmployeeIds(): array
    {
        return Role::query()
            ->where('name', 'Staff Employee')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public static function driverIds(): array
    {
        return Role::query()
            ->where('name', 'like', '%Driver%')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public static function isDriverRoleId($roleId): bool
    {
        $roleId = (int) $roleId;
        if ($roleId <= 0) {
            return false;
        }

        if (in_array($roleId, self::driverIds(), true)) {
            return true;
        }

        $role = Role::find($roleId);

        return $role && stripos((string) $role->name, 'driver') !== false;
    }

    public static function isStaffEmployeeRoleId($roleId): bool
    {
        $roleId = (int) $roleId;
        if ($roleId <= 0) {
            return false;
        }

        if (in_array($roleId, self::staffEmployeeIds(), true)) {
            return true;
        }

        $role = Role::find($roleId);

        return $role && strcasecmp((string) $role->name, 'Staff Employee') === 0;
    }

    /** @return 'driver'|'staff'|null */
    public static function locationTypeForRoleId($roleId): ?string
    {
        if (self::isDriverRoleId($roleId)) {
            return 'driver';
        }
        if (self::isStaffEmployeeRoleId($roleId)) {
            return 'staff';
        }

        return null;
    }

    public static function employeesQuery()
    {
        return \App\Models\User::query()
            ->whereIn('role_id', self::assignableIds())
            ->where(function ($q) {
                $q->where('status', 1)->orWhereNull('status');
            })
            ->orderBy('name');
    }
}
