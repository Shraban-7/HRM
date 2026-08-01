<?php

namespace App\Services\Benefits\Providers;

interface BenefitProviderInterface
{
    /**
     * Provider key used for routing and config.
     */
    public function key(): string;

    /**
     * Human-readable provider name.
     */
    public function label(): string;

    /**
     * Verify a member's eligibility with the provider.
     */
    public function checkEligibility(array $member): bool;

    /**
     * Send an enrollment request to the provider and return a reference/code.
     */
    public function enroll(array $member): array;

    /**
     * Send a cancellation to the provider and return the result.
     */
    public function cancel(string $reference): array;
}
