<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['Super Admin', 'HR Admin', 'Manager', 'Payroll Admin', 'Recruiter', 'Employee', 'Viewer']);
        $slug = strtolower(str_replace(' ', '_', $name));

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => "$name role with standard permissions",
        ];
    }
}