<?php

namespace Tests\Unit\Services;

use App\Services\Payroll\TaxEngine;
use Tests\TestCase;

class TaxEngineTest extends TestCase
{
    protected TaxEngine $taxEngine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taxEngine = new TaxEngine();
    }

    public function test_calculate_tax_within_first_bracket_returns_zero(): void
    {
        $tax = $this->taxEngine->calculateTax(300000);
        $this->assertEquals(0.0, $tax);
    }

    public function test_calculate_tax_across_brackets_returns_cumulative_amount(): void
    {
        // First 300k: 0%, next 300k: 5%, next 150k: 10%
        // 750000 - 300000 = 450000 at 5% = 22500, but capped at 300001-600000
        // (600000-300001) * 0.05 = 14999.95
        // (750000-600000) * 0.10 = 15000
        $tax = $this->taxEngine->calculateTax(750000);
        $this->assertGreaterThan(0, $tax);
        $this->assertEquals(29999.95, $tax); // 14999.95 + 15000
    }

    public function test_get_effective_tax_rate_returns_percentage(): void
    {
        $rate = $this->taxEngine->getEffectiveTaxRate(750000);
        $this->assertGreaterThan(0, $rate);
        $this->assertLessThan(100, $rate);
    }

    public function test_get_effective_tax_rate_returns_zero_for_zero_gross(): void
    {
        $rate = $this->taxEngine->getEffectiveTaxRate(0);
        $this->assertEquals(0.0, $rate);
    }

    public function test_calculate_net_subtracts_tax_and_deductions(): void
    {
        $net = $this->taxEngine->calculateNet(750000, 10000);
        $tax = $this->taxEngine->calculateTax(750000);
        $this->assertEquals(750000 - $tax - 10000, $net);
    }

    public function test_tax_calculation_with_zero_amount(): void
    {
        $tax = $this->taxEngine->calculateTax(0);
        $this->assertEquals(0.0, $tax);
    }

    public function test_calculate_tax_at_max_bracket(): void
    {
        // 3,000,000 salary
        // 300k @ 0% = 0
        // 300k @ 5% = 15000
        // 600k @ 10% = 60000
        // 1200k @ 15% = 180000
        // remaining at 20%
        $tax = $this->taxEngine->calculateTax(3000000);
        $this->assertGreaterThan(255000, $tax);
    }
}