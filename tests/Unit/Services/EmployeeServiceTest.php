<?php

namespace Tests\Unit\Services;

use App\Events\EmployeeHired;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Services\Employee\EmployeeService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeeServiceTest extends TestCase
{
    protected EmployeeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EmployeeService;
    }

    public function test_create_employee_creates_user_and_employee(): void
    {
        Event::fake([EmployeeHired::class]);
        $department = Department::create(['name' => 'Engineering']);

        $employee = $this->service->createEmployee(
            ['email' => 'hire@example.com', 'username' => 'hire_user', 'status' => 'active'],
            ['department_id' => $department->id, 'employee_id' => 'EMP-001', 'hire_date' => '2026-08-01', 'designation' => 'Engineer', 'salary' => 60000]
        );

        $this->assertDatabaseHas('users', ['email' => 'hire@example.com']);
        $this->assertDatabaseHas('employees', ['employee_id' => 'EMP-001', 'department_id' => $department->id]);

        $user = User::where('email', 'hire@example.com')->first();
        $this->assertTrue(Hash::check('changeme123', $user->password));

        Event::assertDispatched(EmployeeHired::class);
    }

    public function test_update_employee_changes_salary(): void
    {
        $user = User::factory()->create();
        $department = Department::create(['name' => 'Sales']);
        $employee = Employee::factory()->create(['user_id' => $user->id, 'department_id' => $department->id, 'salary' => 50000]);

        $updated = $this->service->updateEmployee($employee, ['salary' => 75000]);

        $this->assertEquals(75000, (float) $updated->salary);
    }

    public function test_terminate_employee_sets_status(): void
    {
        $user = User::factory()->create();
        $department = Department::create(['name' => 'Ops']);
        $employee = Employee::factory()->create(['user_id' => $user->id, 'department_id' => $department->id, 'status' => 'active']);

        $this->service->terminateEmployee($employee);

        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'status' => 'terminated']);
    }

    public function test_search_employees_matches_username(): void
    {
        $department = Department::create(['name' => 'HR']);
        $user = User::create(['email' => 'find@example.com', 'username' => 'findable_user', 'password' => Hash::make('password'), 'status' => 'active']);
        Employee::factory()->create(['user_id' => $user->id, 'department_id' => $department->id, 'employee_id' => 'EMP-S-1']);

        $results = $this->service->searchEmployees('findable');

        $this->assertCount(1, $results);
        $this->assertEquals('EMP-S-1', $results->first()->employee_id);
    }
}
