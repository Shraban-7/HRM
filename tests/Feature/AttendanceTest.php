<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Helpers\HashHelper;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    protected function loginAsEmployee(): User
    {
        $salt = HashHelper::generateSalt();
        $user = User::create([
            'email' => 'emp@test.com',
            'username' => 'emp_att',
            'password_hash' => HashHelper::hashPassword('password', $salt),
            'salt' => $salt,
            'status' => 'active',
        ]);

        $role = \App\Models\Role::create(['name' => 'Employee', 'slug' => 'employee']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['employee'],
            'user_permissions' => ['attendance.view', 'attendance.clock_in', 'attendance.clock_out', 'leave.view', 'leave.apply'],
        ]);

        return $user;
    }

    public function test_can_clock_in(): void
    {
        $user = $this->loginAsEmployee();
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson(route('attendance.clock-in'));
        $response->assertStatus(200);
        $this->assertDatabaseHas('attendance', [
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
        ]);
    }

    public function test_can_clock_out(): void
    {
        $user = $this->loginAsEmployee();
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        // First clock in
        $this->postJson(route('attendance.clock-in'));

        // Then clock out
        $response = $this->postJson(route('attendance.clock-out'));
        $response->assertStatus(200);
    }

    public function test_can_submit_leave_request(): void
    {
        $user = $this->loginAsEmployee();
        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'hire_date' => now()->subYear(),
        ]);

        $response = $this->post(route('leave.store'), [
            'type' => 'annual',
            'start_date' => now()->addMonth()->toDateString(),
            'end_date' => now()->addMonth()->addDays(3)->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $employee->id,
            'type' => 'annual',
            'status' => 'pending',
        ]);
    }
}