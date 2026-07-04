<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // defined permissions
        $permissions = [
            'manage_settings',
            'manage_permissions',
            'manage_roles',
            'manage_vehicles',
            'manage_attendance',
            'manage_vendors',
            'manage_salary_slips',
            'manage_employees',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions

        // Role ID 1: Admin (Super Admin) - Handled by Gate::before, but good to have
        $adminRole = Role::firstOrCreate(['id' => 1], ['name' => 'Admin']);
        // Assign all permissions to Admin just in case
        $adminRole->syncPermissions(Permission::all());

        // Role ID 2: HR Admin - Restricted Access
        $hrAdminRole = Role::firstOrCreate(['id' => 2], ['name' => 'HR Admin']);
        
        // Assign specific permissions to HR Admin
        // You can customize this list based on what HR Admin should access
        $hrAdminRole->syncPermissions([
            'manage_employees',
            'manage_attendance',
            'manage_salary_slips',
            'manage_vehicles',
            'manage_vendors',
            // 'manage_settings', // Uncomment if HR Admin can manage settings
        ]);

        // Role ID 3: Learner (Example)
        $learnerRole = Role::firstOrCreate(['id' => 3], ['name' => 'Learner']);
    }
}
