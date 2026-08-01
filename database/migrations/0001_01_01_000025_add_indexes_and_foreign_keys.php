<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('departments') && ! Schema::hasColumn('departments', 'manager_id')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        $indexes = [
            'employees' => ['idx_employees_dept_status', ['department_id', 'status']],
            'attendances' => ['idx_attendance_emp_date_status', ['employee_id', 'date', 'status']],
            'leave_requests' => ['idx_leave_emp_status_start', ['employee_id', 'status', 'start_date']],
            'payrolls' => ['idx_payrolls_status_period', ['status', 'period_start']],
            'payslips' => ['idx_payslips_payroll_emp', ['payroll_id', 'employee_id']],
            'audit_logs' => ['idx_audit_logs_user_action', ['user_id', 'action', 'created_at']],
            'job_posts' => ['idx_job_posts_status_closing', ['status', 'closing_date']],
        ];

        foreach ($indexes as $table => [$name, $columns]) {
            if (! Schema::hasTable($table) || Schema::hasIndex($table, $name)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($name, $columns) {
                $blueprint->index($columns, $name);
            });
        }
    }

    public function down(): void
    {
        $indexes = [
            'employees' => 'idx_employees_dept_status',
            'attendances' => 'idx_attendance_emp_date_status',
            'leave_requests' => 'idx_leave_emp_status_start',
            'payrolls' => 'idx_payrolls_status_period',
            'payslips' => 'idx_payslips_payroll_emp',
            'audit_logs' => 'idx_audit_logs_user_action',
            'job_posts' => 'idx_job_posts_status_closing',
        ];

        foreach ($indexes as $table => $name) {
            if (Schema::hasTable($table) && Schema::hasIndex($table, $name)) {
                Schema::table($table, function (Blueprint $blueprint) use ($name) {
                    $blueprint->dropIndex($name);
                });
            }
        }
    }
};
