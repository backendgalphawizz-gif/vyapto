<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AppUserSeeder extends Seeder
{
    public function run(): void
    {
        $employeeRole = Role::firstOrCreate(['id' => 3], [
            'name' => 'Learner',
            'guard_name' => 'web',
        ]);

        $user = User::updateOrCreate(
            ['email' => 'employee@vyapto.test'],
            [
                'name' => 'App Employee',
                'password' => Hash::make('password'),
                'phone' => '8888888888',
                'address' => 'Indore, MP',
                'role_id' => 3,
                'status' => 1,
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole($employeeRole)) {
            $user->assignRole($employeeRole);
        }

        $this->command?->info('App employee ready.');
        $this->command?->info('Email: employee@vyapto.test');
        $this->command?->info('Password: password');
    }
}
