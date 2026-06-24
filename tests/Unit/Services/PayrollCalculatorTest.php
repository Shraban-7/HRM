<?php

namespace Tests\Unit\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\User;
use App\Services\Payroll\PayrollCalculator;
use App\Services\Payroll\TaxEngine;
use Tests\TestCase;

class PayrollCalculatorTest extends TestCase
{
    protected PayrollCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new PayrollCalculator(new TaxEngine());
    }

    public function test_calculate_employee_pay_returns_correct_gross(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'salary' => 60000,
        ]);

        $result = $this->calculator->calculateEmployeePay($employee, now()->startOfMonth(), now()->endOfMonth());

        $this->assertEquals(60000, $result['gross']);
        $this->assertArrayHasKey('deductions', $result);
        $this->assertArrayHasKey('net', $result);
        $this->assertArrayHasKey('tax', $result);
    }

    public function test_apply_deductions_reduces_net_pay(): void
    {
        $gross = 100000;
        $deductions = $this->calculator->applyDeductions($gross);

        $this->assertGreaterThan(0, $deductions);
        $this->assertEquals($gross * 0.10, $deductions);
    }

    public function test_apply_deductions_with_employee_parameter(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'salary' => 50000,
        ]);

        $deductions = $this->calculator->applyDeductions(50000, $employee);

        $this->assertGreaterThan(0, $deductions);
    }

    public function test_process_payroll_creates_payslips(): void
    {
        $payroll = Payroll::factory()->create([
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'status' => 'draft',
        ]);

        // Create at least one active employee
        $user = User::factory()->create();
        Employee::factory()->active()->create([
            'user_id' => $user->id,
            'salary' => 50000,
        ]);

        $result = $this->calculator->processPayroll($payroll->id);

        $this->assertEquals('completed', $result->status);
        $this->assertGreaterThan(0, $result->total_amount);
        $this->assertGreaterThan(0, $result->payslips->count());
    }
}