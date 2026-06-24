<?php

namespace App\Services\Attendance;

use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Support\Carbon;

class LeaveCalculator
{
    /**
     * Get remaining leave balance for an employee by leave type.
     */
    public function getLeaveBalance(Employee $employee, string $type): int
    {
        $entitlements = config('hrm.leave_types', [
            'annual' => 20,
            'sick' => 14,
            'other' => 5,
        ]);

        $totalEntitlement = $entitlements[$type] ?? 0;
        $used = $this->getUsedLeave($employee, $type);

        return max(0, $totalEntitlement - $used);
    }

    /**
     * Calculate accrued leave days for an employee.
     */
    public function calculateAccrual(Employee $employee, string $type): float
    {
        $entitlements = config('hrm.leave_types', [
            'annual' => 20,
            'sick' => 14,
            'other' => 5,
        ]);

        $annualEntitlement = $entitlements[$type] ?? 0;

        if ($annualEntitlement <= 0) {
            return 0.0;
        }

        // Calculate months since hire date
        $hireDate = Carbon::parse($employee->hire_date);
        $monthsEmployed = $hireDate->diffInMonths(now());

        // Accrue monthly (annual entitlement / 12 months)
        $monthlyAccrual = $annualEntitlement / 12;
        $accrued = round($monthsEmployed * $monthlyAccrual, 1);

        // Cap at annual entitlement
        return min($accrued, (float)$annualEntitlement);
    }

    /**
     * Check if an employee is eligible for a leave request.
     */
    public function isEligibleForLeave(Employee $employee, string $type, int $days): bool
    {
        $balance = $this->getLeaveBalance($employee, $type);

        if ($days > $balance) {
            return false;
        }

        // Check if employee has been employed for at least the probation period (3 months)
        $hireDate = Carbon::parse($employee->hire_date);
        if ($hireDate->diffInMonths(now()) < 3) {
            return false;
        }

        // Check for overlapping leave requests
        // (This would need dates to check properly - handled in the service layer)
        return true;
    }

    /**
     * Calculate the number of working days between two dates (excluding weekends).
     */
    public function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $days = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            if (!$current->isWeekend()) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }

    /**
     * Check if a leave request overlaps with existing approved leaves.
     */
    public function hasOverlappingLeave(Employee $employee, Carbon $startDate, Carbon $endDate, ?int $excludeId = null): bool
    {
        $query = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get used leave count for an employee by type.
     */
    protected function getUsedLeave(Employee $employee, string $type): int
    {
        $yearStart = now()->startOfYear();
        $yearEnd = now()->endOfYear();

        return LeaveRequest::where('employee_id', $employee->id)
            ->where('type', $type)
            ->whereIn('status', ['approved', 'pending'])
            ->whereBetween('start_date', [$yearStart, $yearEnd])
            ->get()
            ->sum(function ($leave) {
                return $this->calculateWorkingDays(
                    Carbon::parse($leave->start_date),
                    Carbon::parse($leave->end_date)
                );
            });
    }
}