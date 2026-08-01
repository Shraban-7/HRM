<?php

namespace Tests\Unit\Services;

use App\Services\Benefits\Providers\BenefitProviderRegistry;
use App\Services\Benefits\Providers\DentalProvider;
use App\Services\Benefits\Providers\HealthProvider;
use InvalidArgumentException;
use Tests\TestCase;

class BenefitProviderRegistryTest extends TestCase
{
    public function test_register_and_resolve_providers(): void
    {
        $registry = new BenefitProviderRegistry;
        $registry->register(new HealthProvider);
        $registry->register(new DentalProvider);

        $this->assertInstanceOf(HealthProvider::class, $registry->provider('health'));
        $this->assertInstanceOf(DentalProvider::class, $registry->provider('dental'));
        $this->assertCount(2, $registry->providers());
    }

    public function test_unknown_provider_throws_exception(): void
    {
        $registry = new BenefitProviderRegistry;

        $this->expectException(InvalidArgumentException::class);

        $registry->provider('vision');
    }

    public function test_health_provider_enrolls_and_cancels(): void
    {
        $provider = new HealthProvider;
        $result = $provider->enroll(['salary' => 50000]);

        $this->assertEquals('health', $result['provider']);
        $this->assertEquals('active', $result['status']);
        $this->assertStringStartsWith('H-', $result['reference']);

        $cancel = $provider->cancel($result['reference']);
        $this->assertEquals('cancelled', $cancel['status']);
    }

    public function test_health_provider_eligibility_requires_salary(): void
    {
        $provider = new HealthProvider;

        $this->assertTrue($provider->checkEligibility(['salary' => 30000, 'date_of_birth' => '1990-01-01']));
        $this->assertFalse($provider->checkEligibility(['salary' => 0]));
    }

    public function test_dental_provider_eligibility_requires_names(): void
    {
        $provider = new DentalProvider;

        $this->assertTrue($provider->checkEligibility(['first_name' => 'Jane', 'last_name' => 'Doe']));
        $this->assertFalse($provider->checkEligibility(['first_name' => '', 'last_name' => 'Doe']));
    }
}
