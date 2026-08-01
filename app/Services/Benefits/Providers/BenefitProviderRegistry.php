<?php

namespace App\Services\Benefits\Providers;

use InvalidArgumentException;

class BenefitProviderRegistry
{
    /** @var array<string, BenefitProviderInterface> */
    protected array $providers = [];

    public function register(BenefitProviderInterface $provider): void
    {
        $this->providers[$provider->key()] = $provider;
    }

    public function provider(string $key): BenefitProviderInterface
    {
        if (! isset($this->providers[$key])) {
            throw new InvalidArgumentException("No benefit provider registered for '{$key}'.");
        }

        return $this->providers[$key];
    }

    public function providers(): array
    {
        return $this->providers;
    }
}
