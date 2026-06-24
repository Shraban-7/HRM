<?php

namespace Database\Factories;

use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $endDate = (clone $startDate)->modify('+' . rand(1, 5) . ' days');

        return [
            'employee_id' => Employee::factory(),
            'type' => $this->faker->randomElement(['annual', 'sick', 'other']),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn() => ['status' => 'approved', 'approved_at' => now()]);
    }

    public function rejected(): static
    {
        return $this->state(fn() => ['status' => 'rejected']);
    }
}