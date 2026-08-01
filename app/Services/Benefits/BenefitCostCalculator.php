<?php

namespace App\Services\Benefits;

use App\Models\BenefitEnrollment;
use App\Models\Employee;

class BenefitCostCalculator
{
    /**
     * Calculate monthly benefit cost breakdown for an employee.
     */
    public function calculateForEmployee(Employee $employee): array
    {
        $enrollments = BenefitEnrollment::with('benefit')
            ->where('employee_id', $employee->id)
            ->where('status', 'active')
            ->get();

        $total = 0.0;
        $byType = [];

        foreach ($enrollments as $enrollment) {
            $cost = (float) ($enrollment->benefit?->cost ?? 0);
            $total += $cost;
            $type = $enrollment->benefit?->type ?? 'other';
            $byType[$type] = round(($byType[$type] ?? 0) + $cost, 2);
        }

        return [
            'enrollments' => $enrollments,
            'total_cost' => round($total, 2),
            'by_type' => $byType,
        ];
    }

    /**
     * Calculate total active benefit cost for all employees.
     */
    public function calculateTotalForAll(): array
    {
        $enrollments = BenefitEnrollment::with(['benefit', 'employee'])
            ->where('status', 'active')
            ->get();

        $total = 0.0;
        $participation = 0;
        $costsByType = [];

        foreach ($enrollments as $enrollment) {
            $cost = (float) ($enrollment->benefit?->cost ?? 0);
            $total += $cost;
            $participation++;
            $type = $enrollment->benefit?->type ?? 'other';
            $costsByType[$type] = round(($costsByType[$type] ?? 0) + $cost, 2);
        }

        return [
            'total_cost' => round($total, 2),
            'participation' => $participation,
            'costs_by_type' => $costsByType,
        ];
    }
}
