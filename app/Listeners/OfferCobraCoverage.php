<?php

namespace App\Listeners;

use App\Events\BenefitEnrollmentChanged;
use App\Services\Benefits\CobraService;
use Illuminate\Contracts\Queue\ShouldQueue;

class OfferCobraCoverage implements ShouldQueue
{
    public function __construct(protected CobraService $cobraService) {}

    public function handle(BenefitEnrollmentChanged $event): void
    {
        if ($event->action !== 'cancelled') {
            return;
        }

        $this->cobraService->createFromEnrollment($event->enrollment);
    }
}
