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
        // Create / Update Super Admin
        $admin = User::updateOrCreate(
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

        // Create / Update HR Admin
        $hrAdmin = User::updateOrCreate(
            ['email' => 'hrmanager@hrm.com'],
            [
                'username' => 'hrmanager',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $hrAdminRole = Role::where('slug', 'hr_admin')->first();
        if ($hrAdminRole && ! $hrAdmin->roles()->where('role_id', $hrAdminRole->id)->exists()) {
            $hrAdmin->roles()->attach($hrAdminRole->id);
        }

        // Create / Update Manager
        $manager = User::updateOrCreate(
            ['email' => 'manager@hrm.com'],
            [
                'username' => 'manager',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );

        $managerRole = Role::where('slug', 'manager')->first();
        if ($managerRole && ! $manager->roles()->where('role_id', $managerRole->id)->exists()) {
            $manager->roles()->attach($managerRole->id);
        }

        // Create / Update regular employee user
        $user = User::updateOrCreate(
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
