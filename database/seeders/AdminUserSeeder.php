<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Helpers\HashHelper;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $salt = HashHelper::generateSalt();

        $admin = User::firstOrCreate(
            ['email' => 'admin@hrm.com'],
            [
                'username' => 'admin',
                'password_hash' => HashHelper::hashPassword('password', $salt),
                'salt' => $salt,
                'status' => 'active',
            ]
        );

        $superAdminRole = Role::where('slug', 'super_admin')->first();
        if ($superAdminRole && !$admin->roles()->where('role_id', $superAdminRole->id)->exists()) {
            $admin->roles()->attach($superAdminRole->id);
        }

        // Create regular user for testing
        $userSalt = HashHelper::generateSalt();
        $user = User::firstOrCreate(
            ['email' => 'user@hrm.com'],
            [
                'username' => 'employee1',
                'password_hash' => HashHelper::hashPassword('password', $userSalt),
                'salt' => $userSalt,
                'status' => 'active',
            ]
        );

        $employeeRole = Role::where('slug', 'employee')->first();
        if ($employeeRole && !$user->roles()->where('role_id', $employeeRole->id)->exists()) {
            $user->roles()->attach($employeeRole->id);
        }
    }
}