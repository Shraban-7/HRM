<?php

namespace Database\Factories;

use App\Models\BenefitEnrollment;
use App\Models\Employee;
use App\Models\Benefit;
use Illuminate\Database\Eloquent\Factories\Factory;

class BenefitEnrollmentFactory extends Factory
{
    protected $model = BenefitEnrollment::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'benefit_id' => Benefit::factory(),
            'enrolled_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'expires_at' => $this->faker->dateTimeBetween('+6 months', '+2 years'),
            'status' => 'active',
        ];
    }
}