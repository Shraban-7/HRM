<?php

namespace Tests\Unit\Services;

use App\Models\Benefit;
use App\Models\BenefitEnrollment;
use App\Models\Employee;
use App\Models\User;
use App\Services\Benefits\BenefitCostCalculator;
use Tests\TestCase;

class BenefitCostCalculatorTest extends TestCase
{
    public function test_calculates_total_cost_for_employee(): void
    {
        $user = User::create(['email' => 'e@test.com', 'username' => 'emp', 'password' => 'password', 'status' => 'active']);
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $health = Benefit::create(['name' => 'Health', 'cost' => 100, 'type' => 'health', 'is_active' => true]);
        $dental = Benefit::create(['name' => 'Dental', 'cost' => 50, 'type' => 'dental', 'is_active' => true]);

        BenefitEnrollment::create(['employee_id' => $employee->id, 'benefit_id' => $health->id, 'enrolled_at' => now(), 'status' => 'active']);
        BenefitEnrollment::create(['employee_id' => $employee->id, 'benefit_id' => $dental->id, 'enrolled_at' => now(), 'status' => 'active']);
        BenefitEnrollment::create(['employee_id' => $employee->id, 'benefit_id' => $dental->id, 'enrolled_at' => now(), 'status' => 'cancelled']);

        $result = app(BenefitCostCalculator::class)->calculateForEmployee($employee);

        $this->assertSame(150.0, $result['total_cost']);
        $this->assertCount(2, $result['enrollments']);
        $this->assertSame(100.0, $result['by_type']['health']);
        $this->assertSame(50.0, $result['by_type']['dental']);
    }
}
