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
