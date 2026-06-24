<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\User;
use App\Helpers\HashHelper;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    protected function loginAsAdmin(): void
    {
        $salt = HashHelper::generateSalt();
        $user = User::create([
            'email' => 'payroll@test.com',
            'username' => 'payroll_admin',
            'password_hash' => HashHelper::hashPassword('password', $salt),
            'salt' => $salt,
            'status' => 'active',
        ]);

        $role = \App\Models\Role::create(['name' => 'Payroll Admin', 'slug' => 'payroll_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['payroll_admin'],
            'user_permissions' => ['payroll.view', 'payroll.run_payroll', 'payslips.view', 'payslips.download'],
        ]);
    }

    public function test_can_view_payroll_index(): void
    {
        $this->loginAsAdmin();
        $response = $this->get(route('payrolls.index'));
        $response->assertStatus(200);
    }

    public function test_can_create_payroll(): void
    {
        $this->loginAsAdmin();
        $response = $this->post(route('payrolls.store'), [
            'period_start' => '2024-01-01',
            'period_end' => '2024-01-31',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('payrolls', [
            'period_start' => '2024-01-01',
            'period_end' => '2024-01-31',
            'status' => 'draft',
        ]);
    }

    public function test_payroll_calculation_returns_payslips(): void
    {
        $this->loginAsAdmin();
        $user = User::create([
            'email' => 'emp@test.com',
            'username' => 'emp_test',
            'password_hash' => HashHelper::hashPassword('password', HashHelper::generateSalt()),
            'salt' => HashHelper::generateSalt(),
            'status' => 'active',
        ]);
        Employee::factory()->active()->create(['user_id' => $user->id, 'salary' => 50000]);

        $payroll = Payroll::factory()->create([
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
        ]);

        $response = $this->post(route('payrolls.calculate', $payroll));
        $response->assertRedirect();
        $this->assertDatabaseHas('payrolls', ['id' => $payroll->id, 'status' => 'completed']);
    }
}