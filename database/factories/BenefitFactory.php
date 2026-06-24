<?php

namespace Database\Factories;

use App\Models\Benefit;
use Illuminate\Database\Eloquent\Factories\Factory;

class BenefitFactory extends Factory
{
    protected $model = Benefit::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Health Insurance', 'Dental Plan', 'Vision Care', 'Life Insurance', 'Retirement Plan', 'Gym Membership', 'Transport Allowance', 'Meal Allowance']),
            'description' => $this->faker->sentence(),
            'cost' => $this->faker->numberBetween(50, 500),
            'type' => $this->faker->randomElement(['health', 'dental', 'vision', 'life', 'retirement', 'other']),
            'is_active' => true,
        ];
    }
}