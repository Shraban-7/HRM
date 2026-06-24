<?php

namespace Database\Factories;

use App\Models\Payroll;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollFactory extends Factory
{
    protected $model = Payroll::class;

    public function definition(): array
    {
        $periodStart = $this->faker->dateTimeBetween('-6 months', 'now');
        $periodEnd = clone $periodStart;
        $periodEnd->modify('+1 month -1 day');

        return [
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end' => $periodEnd->format('Y-m-d'),
            'status' => 'draft',
            'total_amount' => 0,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn() => ['status' => 'completed', 'total_amount' => $this->faker->numberBetween(10000, 500000)]);
    }
}