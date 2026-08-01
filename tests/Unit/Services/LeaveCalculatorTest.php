<?php

namespace Tests\Unit\Services;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\Attendance\LeaveCalculator;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LeaveCalculatorTest extends TestCase
{
    protected LeaveCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new LeaveCalculator;
    }

    protected function makeEmployee(Carbon $hireDate): Employee
    {
        $user = User::factory()->create();

        return Employee::factory()->create([
            'user_id' => $user->id,
            'hire_date' => $hireDate->toDateString(),
        ]);
    }

    public function test_calculate_working_days_excludes_weekends(): void
    {
        $start = Carbon::parse('2026-08-03'); // Monday
        $end = Carbon::parse('2026-08-09');   // Sunday

        $this->assertEquals(5, $this->calculator->calculateWorkingDays($start, $end));
    }

    public function test_get_leave_balance_subtracts_used_leave(): void
    {
        $employee = $this->makeEmployee(Carbon::now()->subYears(2));

        LeaveRequest::factory()->create([
            'employee_id' => $employee->id,
            'type' => 'annual',
            'start_date' => Carbon::now()->subMonth()->startOfWeek()->toDateString(),
            'end_date' => Carbon::now()->subMonth()->startOfWeek()->addDays(4)->toDateString(),
            'status' => 'approved',
        ]);

        $balance = $this->calculator->getLeaveBalance($employee, 'annual');

        $this->assertEquals(20 - 5, $balance);
    }

    public function test_calculate_accrual_caps_at_annual_entitlement(): void
    {
        $employee = $this->makeEmployee(Carbon::now()->subYears(3));

        $accrued = $this->calculator->calculateAccrual($employee, 'annual');

        $this->assertEquals(20.0, $accrued);
    }

    public function test_is_eligible_for_leave_false_during_probation(): void
    {
        $employee = $this->makeEmployee(Carbon::now()->subMonth());

        $this->assertFalse($this->calculator->isEligibleForLeave($employee, 'annual', 2));
    }

    public function test_has_overlapping_leave_detects_conflict(): void
    {
        $employee = $this->makeEmployee(Carbon::now()->subYears(2));

        LeaveRequest::factory()->create([
            'employee_id' => $employee->id,
            'type' => 'annual',
            'start_date' => Carbon::now()->addMonth()->toDateString(),
            'end_date' => Carbon::now()->addMonth()->addDays(3)->toDateString(),
            'status' => 'approved',
        ]);

        $overlap = $this->calculator->hasOverlappingLeave(
            $employee,
            Carbon::now()->addMonth()->addDay(),
            Carbon::now()->addMonth()->addDays(2)
        );

        $this->assertTrue($overlap);
    }
}
