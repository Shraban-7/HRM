<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super_admin', 'description' => 'Full system access'],
            ['name' => 'HR Admin', 'slug' => 'hr_admin', 'description' => 'HR department administration'],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Department manager'],
            ['name' => 'Employee', 'slug' => 'employee', 'description' => 'Regular employee'],
            ['name' => 'Payroll Admin', 'slug' => 'payroll_admin', 'description' => 'Payroll processing access'],
            ['name' => 'Recruiter', 'slug' => 'recruiter', 'description' => 'Recruitment module access'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}