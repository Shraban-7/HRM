<?php

namespace Tests\Unit\Services;

use App\Models\Benefit;
use App\Models\BenefitEnrollment;
use App\Models\CobraCoverage;
use App\Models\Employee;
use App\Models\User;
use App\Services\Benefits\CobraService;
use Carbon\Carbon;
use Tests\TestCase;

class CobraServiceTest extends TestCase
{
    protected CobraService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CobraService;
    }

    public function test_create_from_enrollment_creates_18_month_coverage(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $benefit = Benefit::factory()->create(['cost' => 250]);

        $enrollment = BenefitEnrollment::factory()->create([
            'employee_id' => $employee->id,
            'benefit_id' => $benefit->id,
            'status' => 'cancelled',
        ]);

        $coverage = $this->service->createFromEnrollment($enrollment);

        $this->assertInstanceOf(CobraCoverage::class, $coverage);
        $this->assertEquals('active', $coverage->status);
        $this->assertEquals(250, $coverage->premium_amount);
        $this->assertEquals(
            Carbon::today()->addMonths(CobraService::MAX_CONTINUATION_MONTHS)->toDateString(),
            $coverage->coverage_end_date->toDateString()
        );
    }

    public function test_create_from_enrollment_returns_null_without_benefit(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $benefit = Benefit::factory()->create(['cost' => 100]);

        $enrollment = BenefitEnrollment::factory()->create([
            'employee_id' => $employee->id,
            'benefit_id' => $benefit->id,
            'status' => 'cancelled',
        ]);
        $enrollment->setRelation('benefit', null);

        $this->assertNull($this->service->createFromEnrollment($enrollment));
    }

    public function test_expire_overdue_marks_past_coverages_expired(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $benefit = Benefit::factory()->create(['cost' => 100]);

        CobraCoverage::create([
            'employee_id' => $employee->id,
            'benefit_id' => $benefit->id,
            'qualifying_event_date' => now()->subMonths(20)->toDateString(),
            'coverage_start_date' => now()->subMonths(20)->toDateString(),
            'coverage_end_date' => now()->subMonths(2)->toDateString(),
            'status' => 'active',
            'premium_amount' => 100,
        ]);

        CobraCoverage::create([
            'employee_id' => $employee->id,
            'benefit_id' => $benefit->id,
            'qualifying_event_date' => now()->toDateString(),
            'coverage_start_date' => now()->toDateString(),
            'coverage_end_date' => now()->addMonth()->toDateString(),
            'status' => 'active',
            'premium_amount' => 100,
        ]);

        $updated = $this->service->expireOverdue();

        $this->assertEquals(1, $updated);
        $this->assertEquals(1, CobraCoverage::where('status', 'active')->count());
    }
}
