<?php

namespace App\Services\Benefits\Providers;

use Illuminate\Support\Str;

class DentalProvider implements BenefitProviderInterface
{
    public function key(): string
    {
        return 'dental';
    }

    public function label(): string
    {
        return 'Dental Provider';
    }

    public function checkEligibility(array $member): bool
    {
        return ! empty($member['first_name']) && ! empty($member['last_name']);
    }

    public function enroll(array $member): array
    {
        return [
            'provider' => $this->key(),
            'reference' => 'D-'.Str::upper(Str::random(8)),
            'status' => 'active',
            'message' => "Enrolled with {$this->label()}.",
        ];
    }

    public function cancel(string $reference): array
    {
        return [
            'provider' => $this->key(),
            'reference' => $reference,
            'status' => 'cancelled',
            'message' => "Cancelled coverage with {$this->label()}.",
        ];
    }
}
