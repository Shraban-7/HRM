<?php

namespace App\Services\Payroll;

class TaxEngine
{
    /**
     * Calculate progressive tax for a given gross amount.
     */
    public function calculateTax(float $gross, array $brackets = []): float
    {
        $brackets = $brackets ?: config('hrm.tax_brackets', $this->getDefaultBrackets());
        $tax = 0.0;

        foreach ($brackets as $bracket) {
            $min = (float)$bracket['min'];
            $max = $bracket['max'] !== null ? (float)$bracket['max'] : PHP_FLOAT_MAX;
            $rate = (float)$bracket['rate'];

            if ($gross > $min) {
                $taxableInBracket = min($gross, $max) - $min;
                if ($taxableInBracket > 0) {
                    $tax += $taxableInBracket * $rate;
                }
            }
        }

        return round($tax, 2);
    }

    /**
     * Get the effective tax rate as a percentage.
     */
    public function getEffectiveTaxRate(float $gross): float
    {
        if ($gross <= 0) {
            return 0.0;
        }

        $tax = $this->calculateTax($gross);
        return round(($tax / $gross) * 100, 2);
    }

    /**
     * Calculate net pay after tax.
     */
    public function calculateNet(float $gross, float $deductions = 0.0): float
    {
        $tax = $this->calculateTax($gross);
        return round($gross - $tax - $deductions, 2);
    }

    /**
     * Get default tax brackets from config.
     */
    public function getDefaultBrackets(): array
    {
        return [
            ['min' => 0, 'max' => 300000, 'rate' => 0],
            ['min' => 300001, 'max' => 600000, 'rate' => 0.05],
            ['min' => 600001, 'max' => 1200000, 'rate' => 0.10],
            ['min' => 1200001, 'max' => 2400000, 'rate' => 0.15],
            ['min' => 2400001, 'max' => null, 'rate' => 0.20],
        ];
    }
}