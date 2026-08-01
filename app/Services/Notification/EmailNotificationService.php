<?php

namespace App\Services\Notification;

use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\User;
use App\Notifications\LeaveStatusNotification;
use App\Notifications\PayrollProcessedNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    /**
     * Send welcome email to newly hired employee.
     */
    public function sendWelcomeEmail(User $user): void
    {
        $user->notify(new WelcomeNotification($user));
    }

    /**
     * Send password reset email.
     */
    public function sendPasswordResetEmail(User $user, string $token): void
    {
        $user->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send leave approval notification.
     */
    public function sendLeaveApprovalEmail(LeaveRequest $leave): void
    {
        $employee = $leave->employee;
        $user = $employee?->user;

        if (! $user) {
            return;
        }

        $user->notify(new LeaveStatusNotification($leave));
    }

    /**
     * Send payroll processed notification.
     */
    public function sendPayrollNotification(Payroll $payroll): void
    {
        $payroll->load('payslips.employee.user');

        foreach ($payroll->payslips as $payslip) {
            $user = $payslip->employee?->user;
            if (! $user) {
                continue;
            }

            $user->notify(new PayrollProcessedNotification($payroll, (float) $payslip->net));
        }
    }

    /**
     * Send a generic email notification.
     */
    public function send(array $recipients, string $subject, string $view, array $data = []): void
    {
        foreach ($recipients as $recipient) {
            if ($recipient instanceof User) {
                Mail::send($view, array_merge($data, ['user' => $recipient]), function ($message) use ($recipient, $subject) {
                    $message->to($recipient->email, $recipient->username)
                        ->subject($subject);
                });
            } else {
                Mail::send($view, $data, function ($message) use ($recipient, $subject) {
                    $message->to($recipient['email'] ?? $recipient, $recipient['name'] ?? '')
                        ->subject($subject);
                });
            }
        }
    }
}
