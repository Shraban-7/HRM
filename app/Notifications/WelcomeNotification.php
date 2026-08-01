<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to HRM System!')
            ->view('emails.welcome', [
                'name' => $this->user->username,
                'email' => $this->user->email,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => 'Welcome aboard! Your HRM account has been created.',
            'type' => 'success',
        ];
    }
}
