<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LeaveWorkflowTest extends TestCase
{
    protected function loginAsHrAdmin(): User
    {
        $user = User::create([
            'email' => 'leave-hr@test.com',
            'username' => 'leave_hr',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['leave.view', 'leave.apply', 'leave.approve'],
        ]);

        return $user;
    }

    public function test_can_view_leave_requests(): void
    {
        $this->loginAsHrAdmin();
        LeaveRequest::factory()->create(['type' => 'annual', 'status' => 'pending']);

        $response = $this->get(route('leave.index'));

        $response->assertStatus(200);
    }

    public function test_can_approve_leave_request(): void
    {
        $hr = $this->loginAsHrAdmin();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $leave = LeaveRequest::factory()->create(['employee_id' => $employee->id, 'type' => 'annual', 'status' => 'pending']);

        $response = $this->post(route('leave.approve', $leave));

        $response->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'id' => $leave->id,
            'status' => 'approved',
            'approved_by' => $hr->id,
        ]);
    }

    public function test_can_reject_leave_request(): void
    {
        $hr = $this->loginAsHrAdmin();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $leave = LeaveRequest::factory()->create(['employee_id' => $employee->id, 'type' => 'sick', 'status' => 'pending']);

        $response = $this->post(route('leave.reject', $leave));

        $response->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'id' => $leave->id,
            'status' => 'rejected',
            'approved_by' => $hr->id,
        ]);
    }
}
