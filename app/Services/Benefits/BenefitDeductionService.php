<?php

namespace App\Services\Benefits;

use App\Models\BenefitEnrollment;
use App\Models\Employee;

class BenefitDeductionService
{
    /**
     * Get the monthly benefit deduction total for an employee's active enrollments.
     */
    public function deductionsForEmployee(Employee $employee): float
    {
        return round(
            (float) BenefitEnrollment::with('benefit')
                ->where('employee_id', $employee->id)
                ->where('status', 'active')
                ->get()
                ->sum(fn ($enrollment) => (float) ($enrollment->benefit?->cost ?? 0)),
            2
        );
    }

    /**
     * Get a per-employee breakdown of benefit deductions.
     */
    public function breakdownForAll(): array
    {
        $enrollments = BenefitEnrollment::with(['employee', 'benefit'])
            ->where('status', 'active')
            ->get()
            ->groupBy('employee_id');

        $results = [];

        foreach ($enrollments as $employeeId => $rows) {
            $results[] = [
                'employee_id' => (int) $employeeId,
                'employee_name' => $rows->first()->employee?->user?->username ?? 'N/A',
                'benefits' => $rows->count(),
                'total' => round($rows->sum(fn ($enrollment) => (float) ($enrollment->benefit?->cost ?? 0)), 2),
            ];
        }

        return $results;
    }
}
