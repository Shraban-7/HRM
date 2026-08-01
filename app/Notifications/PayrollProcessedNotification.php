<?php

namespace App\Notifications;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayrollProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payroll $payroll,
        public float $net = 0,
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Payslip is Ready')
            ->view('emails.payroll-processed', [
                'name' => $notifiable->username,
                'period_start' => $this->payroll->period_start,
                'period_end' => $this->payroll->period_end,
                'net' => $this->net,
                'payroll_id' => $this->payroll->id,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => 'Your payslip for the period '.$this->payroll->period_start.' to '.$this->payroll->period_end.' is ready.',
            'type' => 'info',
            'payroll_id' => $this->payroll->id,
        ];
    }
}
