<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JobPost;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Services\Payroll\PayrollCalculator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RunLoadTest extends Command
{
    protected $signature = 'app:load-test {--iterations=10}';

    protected $description = 'Benchmark the heaviest application operations (payroll, reports, dashboards).';

    public function handle(PayrollCalculator $payrollCalculator): int
    {
        $iterations = (int) $this->option('iterations');

        $this->info("Running load test with {$iterations} iterations...\n");

        $results = [];

        $results['Dashboard aggregate counts'] = $this->benchmark(fn () => $this->dashboardCounts(), $iterations);
        $results['Employee list with eager loading'] = $this->benchmark(fn () => Employee::with(['user', 'department'])->paginate(15), $iterations);
        $results['Attendance list with eager loading'] = $this->benchmark(fn () => Attendance::with('employee.user')->orderBy('date', 'desc')->paginate(20), $iterations);
        $results['Leave list with eager loading'] = $this->benchmark(fn () => LeaveRequest::with(['employee.user', 'approver'])->orderBy('created_at', 'desc')->paginate(15), $iterations);
        $results['Payroll list with nested payslips'] = $this->benchmark(fn () => Payroll::with('payslips.employee.user')->orderBy('id', 'desc')->paginate(15), $iterations);
        $results['Report generation (employee-list)'] = $this->benchmark(fn () => app(\App\Jobs\GenerateReport::class), $iterations);
        $results['Payroll calculation'] = $this->benchmarkPayroll($payrollCalculator, $iterations);

        $this->table(['Operation', 'Avg time (ms)', 'Avg queries'], $results);

        return self::SUCCESS;
    }

    protected function benchmark(callable $operation, int $iterations): array
    {
        $times = [];
        $queries = [];

        for ($i = 0; $i < $iterations; $i++) {
            DB::flushQueryLog();
            DB::enableQueryLog();

            $start = hrtime(true);
            $operation();
            $elapsed = (hrtime(true) - $start) / 1e6;

            $times[] = $elapsed;
            $queries[] = count(DB::getQueryLog());

            DB::disableQueryLog();
        }

        return [
            number_format($this->average($times), 2),
            number_format($this->average($queries), 0),
        ];
    }

    protected function benchmarkPayroll(PayrollCalculator $calculator, int $iterations): array
    {
        $payroll = Payroll::where('status', 'draft')->first();
        if (! $payroll) {
            return ['SKIP', 'SKIP'];
        }

        return $this->benchmark(fn () => $calculator->processPayroll($payroll->id), $iterations);
    }

    protected function dashboardCounts(): array
    {
        return [
            'total' => Employee::count(),
            'active' => Employee::where('status', 'active')->count(),
            'departments' => Department::count(),
            'pending_leaves' => LeaveRequest::where('status', 'pending')->count(),
            'running_payrolls' => Payroll::where('status', 'running')->count(),
            'published_jobs' => JobPost::where('status', 'published')->count(),
        ];
    }

    protected function average(array $values): float
    {
        return count($values) ? array_sum($values) / count($values) : 0.0;
    }
}
