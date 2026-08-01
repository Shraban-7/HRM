<?php

namespace App\Services\Benefits\Providers;

use Illuminate\Support\Str;

class HealthProvider implements BenefitProviderInterface
{
    public function key(): string
    {
        return 'health';
    }

    public function label(): string
    {
        return 'Health Provider';
    }

    public function checkEligibility(array $member): bool
    {
        $dob = $member['date_of_birth'] ?? null;
        $salary = (float) ($member['salary'] ?? 0);

        if (! $dob) {
            return false;
        }

        return $salary > 0;
    }

    public function enroll(array $member): array
    {
        return [
            'provider' => $this->key(),
            'reference' => 'H-'.Str::upper(Str::random(8)),
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
