<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $date = $this->faker->dateTimeBetween('-30 days', 'now');
        $clockIn = (clone $date)->setTime(rand(8, 10), rand(0, 59));
        $clockOut = (clone $clockIn)->modify('+' . rand(4, 10) . ' hours');

        return [
            'employee_id' => Employee::factory(),
            'date' => $date->format('Y-m-d'),
            'clock_in' => $clockIn->format('Y-m-d H:i:s'),
            'clock_out' => $clockOut->format('Y-m-d H:i:s'),
            'status' => 'present',
        ];
    }

    public function present(): static
    {
        return $this->state(fn() => ['status' => 'present']);
    }

    public function late(): static
    {
        return $this->state(fn() => ['status' => 'late']);
    }
}