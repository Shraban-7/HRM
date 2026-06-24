<?php

namespace App\Jobs;

use App\Models\Payroll;
use App\Services\Payroll\PayrollCalculator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPayroll implements ShouldQueue
{
    use Queueable;

    protected int $payrollId;

    public function __construct(int $payrollId)
    {
        $this->payrollId = $payrollId;
    }

    public function handle(PayrollCalculator $payrollCalculator): void
    {
        try {
            $payrollCalculator->processPayroll($this->payrollId);

            Log::info('Payroll processed successfully', [
                'payroll_id' => $this->payrollId,
            ]);
        } catch (\Exception $e) {
            Log::error('Payroll processing failed', [
                'payroll_id' => $this->payrollId,
                'error' => $e->getMessage(),
            ]);

            // Mark payroll as failed
            $payroll = Payroll::find($this->payrollId);
            if ($payroll) {
                $payroll->update(['status' => 'failed']);
            }

            throw $e;
        }
    }
}