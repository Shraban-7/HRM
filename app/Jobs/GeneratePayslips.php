<?php

namespace App\Jobs;

use App\Services\Payroll\PayslipGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GeneratePayslips implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300];

    protected int $payrollId;

    public function __construct(int $payrollId)
    {
        $this->payrollId = $payrollId;
    }

    public function handle(PayslipGenerator $payslipGenerator): void
    {
        try {
            $paths = $payslipGenerator->generateBatchPayslips($this->payrollId);

            Log::info('Payslips generated successfully', [
                'payroll_id' => $this->payrollId,
                'count' => count($paths),
            ]);
        } catch (\Exception $e) {
            Log::error('Payslip generation failed', [
                'payroll_id' => $this->payrollId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
