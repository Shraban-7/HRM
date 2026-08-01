<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Payslip;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PayslipTest extends TestCase
{
    protected function loginAsAdmin(): void
    {
        $user = User::create([
            'email' => 'payslip@test.com',
            'username' => 'payslip_admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['payslips.view', 'payslips.download', 'payroll.view'],
        ]);
    }

    public function test_can_view_payslips(): void
    {
        $this->loginAsAdmin();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $payroll = Payroll::factory()->create();
        Payslip::factory()->create(['employee_id' => $employee->id, 'payroll_id' => $payroll->id]);

        $response = $this->get(route('payslips.index'));

        $response->assertStatus(200);
    }

    public function test_can_view_tax_settings(): void
    {
        $this->loginAsAdmin();

        $response = $this->get(route('tax.index'));

        $response->assertStatus(200);
    }

    public function test_employee_can_view_own_payslip(): void
    {
        $user = User::create([
            'email' => 'emp@test.com',
            'username' => 'emp_pay',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $role = Role::create(['name' => 'Employee', 'slug' => 'employee']);
        $user->roles()->attach($role);
        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['employee'],
            'user_permissions' => ['payslips.view', 'payslips.download'],
        ]);

        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $payroll = Payroll::factory()->create();
        $payslip = Payslip::factory()->create(['employee_id' => $employee->id, 'payroll_id' => $payroll->id]);

        $response = $this->get(route('payslips.show', $payslip));

        $response->assertStatus(200);
    }
}
