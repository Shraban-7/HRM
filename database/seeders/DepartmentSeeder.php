<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Human Resources', 'parent_id' => null],
            ['name' => 'Engineering', 'parent_id' => null],
            ['name' => 'Marketing', 'parent_id' => null],
            ['name' => 'Finance', 'parent_id' => null],
            ['name' => 'Operations', 'parent_id' => null],
            ['name' => 'Sales', 'parent_id' => null],
            ['name' => 'Recruitment', 'parent_id' => null],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept['name']], $dept);
        }
    }
}