<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@vyapto.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'),
                'phone' => '9999999999',
                'address' => 'Local Development',
                'role_id' => 1,
                'status' => 1,
                'email_verified_at' => now(),
            ]
        );

        $adminRole = Role::find(1);

        if ($adminRole && ! $admin->hasRole($adminRole)) {
            $admin->assignRole($adminRole);
        }

        $this->command?->info('Admin user ready.');
        $this->command?->info('Email: admin@vyapto.test');
        $this->command?->info('Password: password');
    }
}
