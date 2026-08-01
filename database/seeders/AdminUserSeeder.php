<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@hrm.com'],
            [
                'username' => 'admin',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $superAdminRole = Role::where('slug', 'super_admin')->first();
        if ($superAdminRole && ! $admin->roles()->where('role_id', $superAdminRole->id)->exists()) {
            $admin->roles()->attach($superAdminRole->id);
        }

        // Create regular user for testing
        $user = User::firstOrCreate(
            ['email' => 'user@hrm.com'],
            [
                'username' => 'employee1',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $employeeRole = Role::where('slug', 'employee')->first();
        if ($employeeRole && ! $user->roles()->where('role_id', $employeeRole->id)->exists()) {
            $user->roles()->attach($employeeRole->id);
        }
    }
}
