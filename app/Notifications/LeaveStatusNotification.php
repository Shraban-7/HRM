<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LeaveRequest $leave) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $employee = $this->leave->employee;
        $name = $employee?->user?->username ?? $notifiable->username ?? 'Employee';

        return (new MailMessage)
            ->subject('Leave Request '.ucfirst($this->leave->status))
            ->view('emails.leave-status', [
                'name' => $name,
                'type' => $this->leave->type,
                'start_date' => $this->leave->start_date,
                'end_date' => $this->leave->end_date,
                'status' => $this->leave->status,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => 'Your leave request ('.ucfirst($this->leave->type).') has been '.$this->leave->status.'.',
            'type' => $this->leave->status === 'approved' ? 'success' : 'error',
            'leave_id' => $this->leave->id,
        ];
    }
}
