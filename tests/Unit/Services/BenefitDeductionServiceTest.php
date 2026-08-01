<?php

namespace Tests\Unit\Services;

use App\Models\Benefit;
use App\Models\BenefitEnrollment;
use App\Models\Employee;
use App\Models\User;
use App\Services\Benefits\BenefitDeductionService;
use Tests\TestCase;

class BenefitDeductionServiceTest extends TestCase
{
    protected BenefitDeductionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BenefitDeductionService;
    }

    public function test_deductions_for_employee_sums_active_benefit_costs(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        BenefitEnrollment::factory()->create([
            'employee_id' => $employee->id,
            'benefit_id' => Benefit::factory()->create(['cost' => 100])->id,
        ]);
        BenefitEnrollment::factory()->create([
            'employee_id' => $employee->id,
            'benefit_id' => Benefit::factory()->create(['cost' => 50])->id,
        ]);
        BenefitEnrollment::factory()->create([
            'employee_id' => $employee->id,
            'benefit_id' => Benefit::factory()->create(['cost' => 999])->id,
            'status' => 'cancelled',
        ]);

        $this->assertEquals(150.0, $this->service->deductionsForEmployee($employee));
    }

    public function test_deductions_for_employee_returns_zero_without_enrollments(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $this->assertEquals(0.0, $this->service->deductionsForEmployee($employee));
    }

    public function test_breakdown_for_all_groups_active_enrollments(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        BenefitEnrollment::factory()->create([
            'employee_id' => $employee->id,
            'benefit_id' => Benefit::factory()->create(['cost' => 200])->id,
        ]);

        $breakdown = $this->service->breakdownForAll();

        $this->assertCount(1, $breakdown);
        $this->assertEquals($employee->id, $breakdown[0]['employee_id']);
        $this->assertEquals(200.0, $breakdown[0]['total']);
    }
}
