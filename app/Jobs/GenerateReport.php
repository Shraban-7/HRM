<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\Benefit;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JobPost;
use App\Models\Payroll;
use App\Services\Attendance\LeaveCalculator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateReport implements ShouldQueue
{
    use Queueable;

    protected string $type;

    protected array $parameters;

    protected int $userId;

    public function __construct(string $type, array $parameters, int $userId)
    {
        $this->type = $type;
        $this->parameters = $parameters;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        try {
            $reportData = $this->generateReportData();
            $filename = $this->saveReport($reportData);

            Log::info('Report generated successfully', [
                'type' => $this->type,
                'filename' => $filename,
                'user_id' => $this->userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Report generation failed', [
                'type' => $this->type,
                'error' => $e->getMessage(),
                'user_id' => $this->userId,
            ]);

            throw $e;
        }
    }

    protected function generateReportData(): array
    {
        return match ($this->type) {
            'employee-list' => $this->generateEmployeeList(),
            'payroll-summary' => $this->generatePayrollSummary(),
            'attendance-summary' => $this->generateAttendanceSummary(),
            'leave-balance' => $this->generateLeaveBalance(),
            'department-summary' => $this->generateDepartmentSummary(),
            'recruitment-summary' => $this->generateRecruitmentSummary(),
            'benefits-summary' => $this->generateBenefitsSummary(),
            default => throw new \InvalidArgumentException("Unknown report type: {$this->type}"),
        };
    }

    protected function generateEmployeeList(): array
    {
        $employees = Employee::with('user', 'department')->get();

        return [
            'title' => 'Employee List Report',
            'generated_at' => now()->toDateTimeString(),
            'type' => $this->type,
            'data' => $employees->map(fn ($e) => [
                'id' => $e->employee_id,
                'name' => $e->user?->username ?? 'N/A',
                'email' => $e->user?->email ?? 'N/A',
                'department' => $e->department?->name ?? 'N/A',
                'designation' => $e->designation,
                'status' => $e->status,
                'hire_date' => $e->hire_date,
            ])->toArray(),
        ];
    }

    protected function generatePayrollSummary(): array
    {
        $periodStart = $this->parameters['period_start'] ?? now()->startOfMonth()->toDateString();
        $periodEnd = $this->parameters['period_end'] ?? now()->endOfMonth()->toDateString();

        $payrolls = Payroll::with('payslips')
            ->where('period_start', '>=', $periodStart)
            ->where('period_end', '<=', $periodEnd)
            ->get();

        return [
            'title' => 'Payroll Summary Report',
            'generated_at' => now()->toDateTimeString(),
            'period' => "{$periodStart} to {$periodEnd}",
            'type' => $this->type,
            'data' => $payrolls->map(fn ($p) => [
                'id' => $p->id,
                'period' => "{$p->period_start} to {$p->period_end}",
                'employee_count' => $p->payslips->count(),
                'total_amount' => $p->total_amount,
                'status' => $p->status,
            ])->toArray(),
            'grand_total' => $payrolls->sum('total_amount'),
        ];
    }

    protected function generateAttendanceSummary(): array
    {
        $month = $this->parameters['month'] ?? now()->month;
        $year = $this->parameters['year'] ?? now()->year;
        $departmentId = $this->parameters['department_id'] ?? null;

        $query = Attendance::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with('employee.user', 'employee.department');

        if ($departmentId) {
            $query->whereHas('employee', fn ($q) => $q->where('department_id', $departmentId));
        }

        $records = $query->get();

        return [
            'title' => 'Attendance Summary Report',
            'generated_at' => now()->toDateTimeString(),
            'period' => "{$year}-{$month}",
            'type' => $this->type,
            'data' => $records->groupBy('employee_id')->map(fn ($items, $empId) => [
                'employee' => $items->first()->employee?->user?->username ?? 'N/A',
                'present' => $items->where('status', 'present')->count(),
                'late' => $items->where('status', 'late')->count(),
                'half_day' => $items->where('status', 'half-day')->count(),
                'absent' => $items->where('status', 'absent')->count(),
                'total' => $items->count(),
            ])->toArray(),
        ];
    }

    protected function generateLeaveBalance(): array
    {
        $employees = Employee::with('user')->where('status', 'active')->get();
        $leaveCalculator = app(LeaveCalculator::class);

        return [
            'title' => 'Leave Balance Report',
            'generated_at' => now()->toDateTimeString(),
            'type' => $this->type,
            'data' => $employees->map(fn ($e) => [
                'name' => $e->user?->username ?? 'N/A',
                'annual_remaining' => $leaveCalculator->getLeaveBalance($e, 'annual'),
                'sick_remaining' => $leaveCalculator->getLeaveBalance($e, 'sick'),
                'other_remaining' => $leaveCalculator->getLeaveBalance($e, 'other'),
            ])->toArray(),
        ];
    }

    protected function generateDepartmentSummary(): array
    {
        $departments = Department::withCount(['employees' => fn ($q) => $q->where('status', 'active')])->get();

        return [
            'title' => 'Department Summary Report',
            'generated_at' => now()->toDateTimeString(),
            'type' => $this->type,
            'data' => $departments->map(fn ($d) => [
                'name' => $d->name,
                'active_employees' => $d->employees_count,
                'manager' => $d->manager?->username ?? 'Not assigned',
            ])->toArray(),
            'total_departments' => $departments->count(),
            'total_employees' => $departments->sum('employees_count'),
        ];
    }

    protected function generateRecruitmentSummary(): array
    {
        $jobs = JobPost::withCount([
            'applicants',
            'applicants as hired_count' => fn ($q) => $q->where('status', 'hired'),
        ])->get();

        return [
            'title' => 'Recruitment Summary Report',
            'generated_at' => now()->toDateTimeString(),
            'type' => $this->type,
            'data' => $jobs->map(fn ($job) => [
                'job' => $job->title,
                'status' => $job->status,
                'closing_date' => $job->closing_date ?? 'N/A',
                'applicants' => $job->applicants_count,
                'hired' => $job->hired_count,
            ])->toArray(),
            'total_jobs' => $jobs->count(),
            'total_applicants' => $jobs->sum('applicants_count'),
            'total_hired' => $jobs->sum('hired_count'),
        ];
    }

    protected function generateBenefitsSummary(): array
    {
        $benefits = Benefit::withCount([
            'enrollments as active_enrollments' => fn ($q) => $q->where('status', 'active'),
        ])->get();

        $totalCost = 0.0;

        $data = $benefits->map(function ($benefit) use (&$totalCost) {
            $cost = (float) $benefit->cost;
            $totalCost += $cost * $benefit->active_enrollments;

            return [
                'benefit' => $benefit->name,
                'type' => $benefit->type,
                'cost' => $benefit->cost,
                'enrollments' => $benefit->active_enrollments,
                'monthly_cost' => round($cost * $benefit->active_enrollments, 2),
            ];
        })->toArray();

        return [
            'title' => 'Benefits Summary Report',
            'generated_at' => now()->toDateTimeString(),
            'type' => $this->type,
            'data' => $data,
            'total_monthly_cost' => round($totalCost, 2),
        ];
    }

    protected function saveReport(array $reportData): string
    {
        $filename = "reports/{$this->type}_".now()->format('Ymd_His').'.json';
        Storage::disk('public')->put($filename, json_encode($reportData, JSON_PRETTY_PRINT));

        // Also save as CSV for easy viewing
        $csvFilename = "reports/{$this->type}_".now()->format('Ymd_His').'.csv';
        $this->saveAsCsv($reportData, $csvFilename);

        return $filename;
    }

    protected function saveAsCsv(array $reportData, string $filename): void
    {
        if (empty($reportData['data'])) {
            return;
        }

        $headers = array_keys((array) $reportData['data'][0]);
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, $headers);

        foreach ($reportData['data'] as $row) {
            fputcsv($stream, (array) $row);
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        Storage::disk('public')->put($filename, $csv);
    }
}
