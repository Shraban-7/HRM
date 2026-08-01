<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

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

        $permissionSlugs = [
            'super_admin' => Permission::pluck('slug')->all(),
            'hr_admin' => [
                'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
                'departments.view', 'departments.manage',
                'attendance.view', 'attendance.manage',
                'leave.apply', 'leave.approve',
                'payroll.view', 'payroll.view_payslips',
                'performance.manage_reviews', 'performance.manage_goals', 'performance.submit_feedback',
                'benefits.manage', 'benefits.enroll',
                'dashboard.hr', 'dashboard.employee',
                'audit.view',
            ],
            'manager' => [
                'employees.view',
                'departments.view',
                'attendance.view',
                'leave.apply', 'leave.approve',
                'payroll.view_payslips',
                'performance.submit_feedback',
                'dashboard.manager', 'dashboard.employee',
            ],
            'employee' => [
                'attendance.view',
                'leave.apply',
                'payroll.view_payslips',
                'performance.submit_feedback',
                'benefits.enroll',
                'dashboard.employee',
            ],
            'payroll_admin' => [
                'employees.view',
                'attendance.view',
                'payroll.view', 'payroll.run_payroll', 'payroll.view_payslips',
                'dashboard.hr', 'dashboard.employee',
                'audit.view',
            ],
            'recruiter' => [
                'employees.view',
                'recruitment.manage_jobs', 'recruitment.manage_applicants', 'recruitment.schedule_interviews',
                'dashboard.employee',
            ],
        ];

        foreach ($permissionSlugs as $slug => $perms) {
            $role = Role::where('slug', $slug)->first();

            if (! $role) {
                continue;
            }

            $permissionIds = Permission::whereIn('slug', $perms)->pluck('id')->all();
            $role->permissions()->syncWithoutDetaching($permissionIds);
        }
    }
}
