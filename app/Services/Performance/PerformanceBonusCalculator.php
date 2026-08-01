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
     *
     * @param  int|null  $rating  Optional pre-computed rating to avoid an extra query.
     */
    public function calculateBonusForEmployee(Employee $employee, ?int $rating = null): float
    {
        $rating ??= $this->getLatestRating($employee);

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

        // Eager load user so no lazy-loading is triggered per employee.
        $employees = Employee::with('user')->where('status', 'active')->get();

        // Pre-fetch the latest rated review for every active employee in one query.
        $latestRatings = PerformanceReview::whereIn('employee_id', $employees->pluck('id'))
            ->whereNotNull('rating')
            ->orderBy('review_date', 'desc')
            ->get()
            ->unique('employee_id');

        foreach ($employees as $employee) {
            $rating = $latestRatings->firstWhere('employee_id', $employee->id)?->rating;
            $bonus = $this->calculateBonusForEmployee($employee, $rating);

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
