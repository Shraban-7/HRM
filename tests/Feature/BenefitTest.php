<?php

namespace Tests\Feature;

use App\Models\Benefit;
use App\Models\BenefitEnrollment;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BenefitTest extends TestCase
{
    protected function loginAsEmployee(): User
    {
        $user = User::create([
            'email' => 'benefits@test.com',
            'username' => 'benefits_user',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'Employee', 'slug' => 'employee']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['employee'],
            'user_permissions' => ['benefits.view', 'benefits.enroll'],
        ]);

        return $user;
    }

    public function test_can_view_benefit_catalog(): void
    {
        $this->loginAsEmployee();
        Benefit::create(['name' => 'Health Insurance', 'description' => 'Medical cover', 'cost' => 150, 'type' => 'health', 'is_active' => true]);

        $response = $this->get(route('benefits.index'));

        $response->assertStatus(200);
        $response->assertSee('Health Insurance');
    }

    public function test_can_enroll_in_benefit(): void
    {
        $user = $this->loginAsEmployee();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $benefit = Benefit::create(['name' => 'Dental Plan', 'description' => 'Dental cover', 'cost' => 80, 'type' => 'dental', 'is_active' => true]);

        $response = $this->post(route('enrollments.store'), [
            'benefit_id' => $benefit->id,
            'enrolled_at' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('benefit_enrollments', [
            'employee_id' => $employee->id,
            'benefit_id' => $benefit->id,
            'status' => 'active',
        ]);
    }

    public function test_can_cancel_enrollment(): void
    {
        $user = $this->loginAsEmployee();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $benefit = Benefit::create(['name' => 'Vision Care', 'description' => 'Eye cover', 'cost' => 40, 'type' => 'vision', 'is_active' => true]);

        $enrollment = BenefitEnrollment::create([
            'employee_id' => $employee->id,
            'benefit_id' => $benefit->id,
            'enrolled_at' => now(),
            'status' => 'active',
        ]);

        $response = $this->delete(route('enrollments.destroy', $enrollment));

        $response->assertRedirect();
        $this->assertDatabaseHas('benefit_enrollments', [
            'id' => $enrollment->id,
            'status' => 'cancelled',
        ]);
    }
}
