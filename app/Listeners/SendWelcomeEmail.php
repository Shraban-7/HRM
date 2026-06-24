<?php

namespace App\Listeners;

use App\Events\EmployeeHired;
use App\Services\Notification\EmailNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    protected EmailNotificationService $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function handle(EmployeeHired $event): void
    {
        $user = $event->employee->user;

        if ($user) {
            $this->emailService->sendWelcomeEmail($user);
        }
    }
}