<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    protected function loginAsAdmin(): void
    {
        $user = User::create([
            'email' => 'dept@test.com',
            'username' => 'dept_admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['departments.view', 'departments.create', 'departments.edit', 'departments.delete'],
        ]);
    }

    public function test_can_list_departments(): void
    {
        $this->loginAsAdmin();
        Department::create(['name' => 'Engineering']);

        $response = $this->get(route('departments.index'));

        $response->assertStatus(200);
        $response->assertSee('Engineering');
    }

    public function test_can_create_department(): void
    {
        $this->loginAsAdmin();

        $response = $this->post(route('departments.store'), [
            'name' => 'Human Resources',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['name' => 'Human Resources']);
    }

    public function test_can_update_department(): void
    {
        $this->loginAsAdmin();
        $department = Department::create(['name' => 'IT']);

        $response = $this->put(route('departments.update', $department), [
            'name' => 'Information Technology',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', ['id' => $department->id, 'name' => 'Information Technology']);
    }

    public function test_can_delete_department(): void
    {
        $this->loginAsAdmin();
        $department = Department::create(['name' => 'Temp Dept']);

        $response = $this->delete(route('departments.destroy', $department));

        $response->assertRedirect();
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_cannot_create_duplicate_department(): void
    {
        $this->loginAsAdmin();
        Department::create(['name' => 'Finance']);

        $response = $this->post(route('departments.store'), ['name' => 'Finance']);

        $response->assertSessionHasErrors('name');
    }

    public function test_cannot_assign_self_as_parent(): void
    {
        $this->loginAsAdmin();
        $department = Department::create(['name' => 'Operations']);

        $response = $this->put(route('departments.update', $department), [
            'name' => 'Operations',
            'parent_id' => $department->id,
        ]);

        $response->assertSessionHasErrors('parent_id');
    }
}
