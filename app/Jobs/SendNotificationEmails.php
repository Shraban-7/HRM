<?php

namespace App\Jobs;

use App\Services\Notification\EmailNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendNotificationEmails implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [30, 120];

    protected array $recipients;

    protected string $subject;

    protected string $view;

    protected array $data;

    public function __construct(array $recipients, string $subject, string $view, array $data = [])
    {
        $this->recipients = $recipients;
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
    }

    public function handle(EmailNotificationService $emailService): void
    {
        try {
            $emailService->send($this->recipients, $this->subject, $this->view, $this->data);

            Log::info('Bulk notification emails sent', [
                'recipient_count' => count($this->recipients),
                'subject' => $this->subject,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send notification emails', [
                'error' => $e->getMessage(),
                'subject' => $this->subject,
            ]);

            throw $e;
        }
    }
}
