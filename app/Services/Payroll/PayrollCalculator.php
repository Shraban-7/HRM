<?php

namespace App\Services\Payroll;

use App\Events\PayrollProcessed;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\Benefits\BenefitDeductionService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollCalculator
{
    protected TaxEngine $taxEngine;

    protected BenefitDeductionService $benefitDeductions;

    public function __construct(TaxEngine $taxEngine, BenefitDeductionService $benefitDeductions)
    {
        $this->taxEngine = $taxEngine;
        $this->benefitDeductions = $benefitDeductions;
    }

    /**
     * Calculate payroll for all active employees in a payroll period.
     */
    public function calculatePayroll(int $payrollId): array
    {
        $payroll = Payroll::with('payslips.employee.user')->findOrFail($payrollId);
        $employees = Employee::where('status', 'active')->get();
        $results = [];

        foreach ($employees as $employee) {
            $results[] = $this->calculateEmployeePay(
                $employee,
                Carbon::parse($payroll->period_start),
                Carbon::parse($payroll->period_end)
            );
        }

        return $results;
    }

    /**
     * Calculate pay for a single employee.
     */
    public function calculateEmployeePay(Employee $employee, Carbon $periodStart, Carbon $periodEnd): array
    {
        $gross = $this->getMonthlySalary($employee, $periodStart, $periodEnd);
        $deductions = $this->applyDeductions($gross, $employee);
        $tax = $this->taxEngine->calculateTax($gross);
        $totalDeductions = $deductions + $tax;
        $net = round($gross - $totalDeductions, 2);

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->user?->username ?? 'N/A',
            'gross' => $gross,
            'deductions' => $deductions,
            'tax' => $tax,
            'total_deductions' => $totalDeductions,
            'net' => $net,
        ];
    }

    /**
     * Apply configurable deductions to gross pay.
     */
    public function applyDeductions(float $gross, ?Employee $employee = null): float
    {
        $deductionRate = config('hrm.default_deduction_rate', 0.10);
        $baseDeduction = round($gross * $deductionRate, 2);

        // Benefit-based deductions for the employee's active enrollments.
        $additionalDeductions = $employee
            ? $this->benefitDeductions->deductionsForEmployee($employee)
            : 0.0;

        return round($baseDeduction + $additionalDeductions, 2);
    }

    /**
     * Process the full payroll run: calculate, create payslips, update payroll.
     */
    public function processPayroll(int $payrollId): Payroll
    {
        return DB::transaction(function () use ($payrollId) {
            $payroll = Payroll::findOrFail($payrollId);
            $results = $this->calculatePayroll($payrollId);
            $totalAmount = 0;

            // Delete existing payslips for this payroll (if recalculating)
            $payroll->payslips()->delete();

            foreach ($results as $result) {
                $payroll->payslips()->create([
                    'employee_id' => $result['employee_id'],
                    'gross' => $result['gross'],
                    'deductions' => $result['total_deductions'],
                    'net' => $result['net'],
                    'issued_at' => now(),
                ]);
                $totalAmount += $result['net'];
            }

            $payroll->update([
                'total_amount' => round($totalAmount, 2),
                'status' => 'completed',
            ]);

            event(new PayrollProcessed($payroll));

            return $payroll->fresh('payslips.employee.user');
        });
    }

    /**
     * Get monthly salary. For full periods, returns full salary.
     * For partial periods, would prorate based on days worked.
     */
    protected function getMonthlySalary(Employee $employee, Carbon $periodStart, Carbon $periodEnd): float
    {
        // Full period salary
        return (float) $employee->salary;
    }
}
