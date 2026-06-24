<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Engineering', 'Marketing', 'Sales', 'Human Resources', 'Finance', 'Operations', 'Legal', 'IT', 'Design', 'Support']),
            'parent_id' => null,
            'manager_id' => null,
        ];
    }
}