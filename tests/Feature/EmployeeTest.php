<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use App\Helpers\HashHelper;
use Tests\TestCase;

class EmployeeTest extends TestCase
{
    protected function loginAsAdmin(): void
    {
        $salt = HashHelper::generateSalt();
        $user = User::create([
            'email' => 'admin@test.com',
            'username' => 'admin',
            'password_hash' => HashHelper::hashPassword('password', $salt),
            'salt' => $salt,
            'status' => 'active',
        ]);

        $role = \App\Models\Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
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
}