<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\JobPost;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicantFactory extends Factory
{
    protected $model = Applicant::class;

    public function definition(): array
    {
        return [
            'job_post_id' => JobPost::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'resume_path' => null,
            'status' => $this->faker->randomElement(['applied', 'screening', 'interviewing', 'offered', 'hired', 'rejected']),
        ];
    }
}