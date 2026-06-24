<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $modules = ['employees', 'departments', 'payroll', 'payslips', 'attendance', 'leave', 'recruitment', 'performance', 'benefits', 'audit', 'settings'];
        $actions = ['view', 'create', 'edit', 'delete', 'manage', 'approve', 'run_payroll', 'apply', 'enroll', 'download', 'clock_in', 'clock_out'];
        $module = $this->faker->randomElement($modules);
        $action = $this->faker->randomElement($actions);

        return [
            'name' => ucfirst($module) . ' ' . ucfirst($action),
            'slug' => $module . '.' . $action,
            'module' => $module,
        ];
    }
}