<?php

namespace Database\Factories;

use App\Models\JobPost;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobPostFactory extends Factory
{
    protected $model = JobPost::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraph(3),
            'requirements' => $this->faker->paragraph(2),
            'status' => $this->faker->randomElement(['draft', 'open', 'closed']),
            'closing_date' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
        ];
    }
}