<?php

namespace App\Events;

use App\Models\BenefitEnrollment;
use Illuminate\Foundation\Events\Dispatchable;

class BenefitEnrollmentChanged
{
    use Dispatchable;

    public BenefitEnrollment $enrollment;

    public string $action;

    public function __construct(BenefitEnrollment $enrollment, string $action = 'created')
    {
        $this->enrollment = $enrollment;
        $this->action = $action;
    }
}
