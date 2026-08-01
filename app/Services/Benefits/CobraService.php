<?php

namespace App\Services\Benefits;

use App\Models\BenefitEnrollment;
use App\Models\CobraCoverage;
use Carbon\Carbon;

class CobraService
{
    /**
     * COBRA continuation period in days (US standard is 18/36 months).
     */
    public const MAX_CONTINUATION_MONTHS = 18;

    /**
     * Create a COBRA coverage record for a cancelled enrollment.
     */
    public function createFromEnrollment(BenefitEnrollment $enrollment): ?CobraCoverage
    {
        $benefit = $enrollment->benefit;

        if (! $benefit) {
            return null;
        }

        $today = Carbon::today();
        $coverageEnd = $today->copy()->addMonths(self::MAX_CONTINUATION_MONTHS);

        return CobraCoverage::create([
            'employee_id' => $enrollment->employee_id,
            'benefit_id' => $benefit->id,
            'benefit_enrollment_id' => $enrollment->id,
            'qualifying_event_date' => $today,
            'coverage_start_date' => $today,
            'coverage_end_date' => $coverageEnd,
            'status' => 'active',
            'premium_amount' => (float) ($benefit->cost ?? 0),
        ]);
    }

    /**
     * Expire any COBRA coverages whose coverage period has ended.
     */
    public function expireOverdue(): int
    {
        return CobraCoverage::where('status', 'active')
            ->where('coverage_end_date', '<', Carbon::today())
            ->update(['status' => 'expired']);
    }

    /**
     * Terminate an active COBRA coverage.
     */
    public function terminate(CobraCoverage $coverage): bool
    {
        return $coverage->update(['status' => 'terminated']);
    }
}
