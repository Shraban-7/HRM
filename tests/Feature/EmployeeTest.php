<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    protected function loginAsAdmin(): void
    {
        $user = User::create([
            'email' => 'admin@test.com',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['employees.view', 'employees.create', 'employees.edit', 'employees.delete'],
        ]);
    }

    public function test_can_list_employees(): void
    {
        $this->loginAsAdmin();
        Employee::factory()->count(3)->create();
        $response = $this->get(route('employees.index'));
        $response->assertStatus(200);
    }

    public function test_can_create_employee(): void
    {
        $this->loginAsAdmin();
        $department = Department::create(['name' => 'Engineering']);
        $response = $this->post(route('employees.store'), [
            'email' => 'newemp@test.com',
            'username' => 'newemp',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'department_id' => $department->id,
            'employee_id' => 'EMP-TEST-001',
            'hire_date' => '2024-01-15',
            'designation' => 'Software Engineer',
            'salary' => 50000,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['employee_id' => 'EMP-TEST-001']);
    }

    public function test_can_view_employee(): void
    {
        $this->loginAsAdmin();
        $employee = Employee::factory()->create();
        $response = $this->get(route('employees.show', $employee));
        $response->assertStatus(200);
    }

    public function test_can_update_employee(): void
    {
        $this->loginAsAdmin();
        $department = Department::create(['name' => 'Marketing']);
        $employee = Employee::factory()->create(['department_id' => $department->id]);
        $response = $this->put(route('employees.update', $employee), [
            'department_id' => $department->id,
            'designation' => 'Senior Engineer',
            'salary' => 75000,
            'status' => 'active',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'designation' => 'Senior Engineer']);
    }

    public function test_can_terminate_employee(): void
    {
        $this->loginAsAdmin();
        $employee = Employee::factory()->create();
        $response = $this->delete(route('employees.destroy', $employee));
        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'status' => 'terminated']);
    }

    public function test_can_search_employees(): void
    {
        $this->loginAsAdmin();
        Employee::factory()->count(2)->create();
        $user = User::create([
            'email' => 'srch@test.com',
            'username' => 'sherlock_hr',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        Employee::factory()->create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-SRCH-001',
            'designation' => 'Analyst',
        ]);

        $response = $this->getJson(route('employees.search', ['q' => 'sherlock_hr']));
        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['employee_id' => 'EMP-SRCH-001']);
    }
}
