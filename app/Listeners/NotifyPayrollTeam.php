<?php

namespace App\Listeners;

use App\Events\PayrollProcessed;
use App\Models\User;
use App\Services\Notification\InAppNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyPayrollTeam implements ShouldQueue
{
    use InteractsWithQueue;

    protected InAppNotificationService $notificationService;

    public function __construct(InAppNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(PayrollProcessed $event): void
    {
        $payroll = $event->payroll;

        // Notify users with payroll_admin or hr_admin roles
        $payrollTeam = User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['payroll_admin', 'hr_admin', 'super_admin']);
        })->get();

        $message = "Payroll #{$payroll->id} for period {$payroll->period_start} to {$payroll->period_end} has been processed. Total amount: \${$payroll->total_amount}";

        $this->notificationService->notifyMany($payrollTeam->all(), $message, 'success');
    }
}