<?php

namespace Database\Factories;

use App\Models\PerformanceReview;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerformanceReviewFactory extends Factory
{
    protected $model = PerformanceReview::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'reviewer_id' => User::factory(),
            'review_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'rating' => $this->faker->numberBetween(1, 5),
            'notes' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'completed']),
        ];
    }
}