<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Employee module
            ['name' => 'View Employees', 'slug' => 'employees.view', 'module' => 'employees'],
            ['name' => 'Create Employees', 'slug' => 'employees.create', 'module' => 'employees'],
            ['name' => 'Edit Employees', 'slug' => 'employees.edit', 'module' => 'employees'],
            ['name' => 'Delete Employees', 'slug' => 'employees.delete', 'module' => 'employees'],
            // Department module
            ['name' => 'View Departments', 'slug' => 'departments.view', 'module' => 'departments'],
            ['name' => 'Manage Departments', 'slug' => 'departments.manage', 'module' => 'departments'],
            // Payroll module
            ['name' => 'View Payroll', 'slug' => 'payroll.view', 'module' => 'payroll'],
            ['name' => 'Run Payroll', 'slug' => 'payroll.run_payroll', 'module' => 'payroll'],
            ['name' => 'View Payslips', 'slug' => 'payroll.view_payslips', 'module' => 'payroll'],
            // Attendance module
            ['name' => 'View Attendance', 'slug' => 'attendance.view', 'module' => 'attendance'],
            ['name' => 'Manage Attendance', 'slug' => 'attendance.manage', 'module' => 'attendance'],
            // Leave module
            ['name' => 'Apply Leave', 'slug' => 'leave.apply', 'module' => 'leave'],
            ['name' => 'Approve Leave', 'slug' => 'leave.approve', 'module' => 'leave'],
            // Recruitment module
            ['name' => 'Manage Jobs', 'slug' => 'recruitment.manage_jobs', 'module' => 'recruitment'],
            ['name' => 'Manage Applicants', 'slug' => 'recruitment.manage_applicants', 'module' => 'recruitment'],
            ['name' => 'Schedule Interviews', 'slug' => 'recruitment.schedule_interviews', 'module' => 'recruitment'],
            // Performance module
            ['name' => 'Manage Reviews', 'slug' => 'performance.manage_reviews', 'module' => 'performance'],
            ['name' => 'Manage Goals', 'slug' => 'performance.manage_goals', 'module' => 'performance'],
            ['name' => 'Submit Feedback', 'slug' => 'performance.submit_feedback', 'module' => 'performance'],
            // Benefits module
            ['name' => 'Manage Benefits', 'slug' => 'benefits.manage', 'module' => 'benefits'],
            ['name' => 'Enroll Benefits', 'slug' => 'benefits.enroll', 'module' => 'benefits'],
            // Dashboard
            ['name' => 'View HR Dashboard', 'slug' => 'dashboard.hr', 'module' => 'dashboard'],
            ['name' => 'View Manager Dashboard', 'slug' => 'dashboard.manager', 'module' => 'dashboard'],
            ['name' => 'View Employee Dashboard', 'slug' => 'dashboard.employee', 'module' => 'dashboard'],
            // Audit
            ['name' => 'View Audit Logs', 'slug' => 'audit.view', 'module' => 'audit'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }
    }
}