<?php

namespace Tests\Unit\Services;

use App\Models\Employee;
use App\Models\PerformanceReview;
use App\Models\User;
use App\Services\Performance\PerformanceBonusCalculator;
use Tests\TestCase;

class PerformanceBonusCalculatorTest extends TestCase
{
    protected PerformanceBonusCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new PerformanceBonusCalculator;
    }

    public function test_get_bonus_rate_maps_ratings(): void
    {
        $this->assertEquals(0.10, $this->calculator->getBonusRate(5));
        $this->assertEquals(0.05, $this->calculator->getBonusRate(4));
        $this->assertEquals(0.02, $this->calculator->getBonusRate(3));
        $this->assertEquals(0.0, $this->calculator->getBonusRate(2));
        $this->assertEquals(0.0, $this->calculator->getBonusRate(1));
        $this->assertEquals(0.0, $this->calculator->getBonusRate(99));
    }

    public function test_calculate_bonus_uses_latest_review_rating(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'salary' => 100000,
        ]);

        PerformanceReview::factory()->create([
            'employee_id' => $employee->id,
            'rating' => 5,
            'review_date' => now()->subMonths(2),
        ]);

        PerformanceReview::factory()->create([
            'employee_id' => $employee->id,
            'rating' => 3,
            'review_date' => now(),
        ]);

        $bonus = $this->calculator->calculateBonusForEmployee($employee);

        $this->assertEquals(100000 * 0.02, $bonus);
    }

    public function test_calculate_bonus_returns_zero_without_reviews(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'salary' => 100000,
        ]);

        $this->assertEquals(0.0, $this->calculator->calculateBonusForEmployee($employee));
    }

    public function test_calculate_bonuses_for_all_skips_unrated_employees(): void
    {
        $user = User::factory()->create();
        $rated = Employee::factory()->active()->create(['user_id' => $user->id, 'salary' => 50000]);
        $unrated = Employee::factory()->active()->create(['salary' => 50000]);

        PerformanceReview::factory()->create([
            'employee_id' => $rated->id,
            'rating' => 4,
        ]);

        $results = $this->calculator->calculateBonusesForAll();
        $ids = array_column($results, 'employee_id');

        $this->assertContains($rated->id, $ids);
        $this->assertNotContains($unrated->id, $ids);
    }
}
