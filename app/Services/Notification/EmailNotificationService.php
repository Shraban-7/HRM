<?php

namespace App\Services\Notification;

use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    /**
     * Send welcome email to newly hired employee.
     */
    public function sendWelcomeEmail(User $user): void
    {
        $data = [
            'name' => $user->username,
            'email' => $user->email,
        ];

        Mail::send('emails.welcome', $data, function ($message) use ($user) {
            $message->to($user->email, $user->username)
                    ->subject('Welcome to the Company!');
        });
    }

    /**
     * Send password reset email.
     */
    public function sendPasswordResetEmail(User $user, string $token): void
    {
        $data = [
            'name' => $user->username,
            'email' => $user->email,
            'token' => $token,
            'resetUrl' => route('auth.reset-password', ['token' => $token]),
        ];

        Mail::send('emails.password-reset', $data, function ($message) use ($user) {
            $message->to($user->email, $user->username)
                    ->subject('Reset Your Password');
        });
    }

    /**
     * Send leave approval notification.
     */
    public function sendLeaveApprovalEmail(LeaveRequest $leave): void
    {
        $employee = $leave->employee;
        $user = $employee->user;

        if (!$user) {
            return;
        }

        $data = [
            'name' => $user->username,
            'type' => $leave->type,
            'start_date' => $leave->start_date,
            'end_date' => $leave->end_date,
            'status' => $leave->status,
        ];

        $subject = $leave->status === 'approved'
            ? 'Leave Request Approved'
            : 'Leave Request Rejected';

        Mail::send('emails.leave-status', $data, function ($message) use ($user, $subject) {
            $message->to($user->email, $user->username)
                    ->subject($subject);
        });
    }

    /**
     * Send payroll processed notification.
     */
    public function sendPayrollNotification(Payroll $payroll): void
    {
        // Notify all active employees with payslips in this payroll
        $payroll->load('payslips.employee.user');

        foreach ($payroll->payslips as $payslip) {
            $user = $payslip->employee->user;
            if (!$user) {
                continue;
            }

            $data = [
                'name' => $user->username,
                'period_start' => $payroll->period_start,
                'period_end' => $payroll->period_end,
                'net' => $payslip->net,
                'payroll_id' => $payroll->id,
            ];

            Mail::send('emails.payroll-processed', $data, function ($message) use ($user) {
                $message->to($user->email, $user->username)
                        ->subject('Your Payslip is Ready');
            });
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