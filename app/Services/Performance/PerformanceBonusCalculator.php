<?php

namespace App\Services\Performance;

use App\Models\Employee;
use App\Models\PerformanceReview;

class PerformanceBonusCalculator
{
    /**
     * Get bonus rate (fraction of monthly salary) for a given rating.
     */
    public function getBonusRate(int $rating): float
    {
        $rates = config('hrm.performance_bonus_rates', [
            5 => 0.10,
            4 => 0.05,
            3 => 0.02,
            2 => 0.0,
            1 => 0.0,
        ]);

        return (float) ($rates[$rating] ?? 0.0);
    }

    /**
     * Calculate performance bonus for an employee based on their latest review.
     */
    public function calculateBonusForEmployee(Employee $employee): float
    {
        $rating = $this->getLatestRating($employee);

        if ($rating === null) {
            return 0.0;
        }

        return round((float) $employee->salary * $this->getBonusRate($rating), 2);
    }

    /**
     * Calculate bonuses for all active employees.
     */
    public function calculateBonusesForAll(): array
    {
        $results = [];

        foreach (Employee::where('status', 'active')->get() as $employee) {
            $rating = $this->getLatestRating($employee);
            $bonus = $this->calculateBonusForEmployee($employee);

            if ($rating !== null) {
                $results[] = [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->user?->username ?? 'N/A',
                    'rating' => $rating,
                    'salary' => (float) $employee->salary,
                    'bonus' => $bonus,
                ];
            }
        }

        return $results;
    }

    protected function getLatestRating(Employee $employee): ?int
    {
        $review = PerformanceReview::where('employee_id', $employee->id)
            ->whereNotNull('rating')
            ->latest('review_date')
            ->first();

        return $review?->rating;
    }
}
