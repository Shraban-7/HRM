<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'employee_id' => 'EMP-'.strtoupper(uniqid()),
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'designation' => $this->faker->randomElement(['Software Engineer', 'Project Manager', 'HR Coordinator', 'Marketing Analyst', 'Accountant', 'Designer']),
            'salary' => $this->faker->numberBetween(30000, 120000),
            'status' => 'active',
            'employment_type' => 'full_time',
            'profile_photo_path' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }

    public function terminated(): static
    {
        return $this->state(fn () => ['status' => 'terminated']);
    }
}
