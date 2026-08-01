<?php

namespace Tests\Feature;

use App\Models\Benefit;
use App\Models\CobraCoverage;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CobraTest extends TestCase
{
    protected function loginAsAdmin(): void
    {
        $user = User::create([
            'email' => 'cobra@test.com',
            'username' => 'cobra_admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['benefits.manage'],
        ]);
    }

    public function test_can_list_cobra_coverages(): void
    {
        $this->loginAsAdmin();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $benefit = Benefit::factory()->create(['cost' => 200]);

        CobraCoverage::create([
            'employee_id' => $employee->id,
            'benefit_id' => $benefit->id,
            'qualifying_event_date' => now()->toDateString(),
            'coverage_start_date' => now()->toDateString(),
            'coverage_end_date' => now()->addMonths(18)->toDateString(),
            'status' => 'active',
            'premium_amount' => 200,
        ]);

        $response = $this->get(route('cobra.index'));

        $response->assertStatus(200);
        $response->assertSee($user->username);
    }

    public function test_can_create_cobra_coverage(): void
    {
        $this->loginAsAdmin();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $benefit = Benefit::factory()->create(['cost' => 200]);

        $response = $this->post(route('cobra.store'), [
            'employee_id' => $employee->id,
            'benefit_id' => $benefit->id,
            'qualifying_event_date' => now()->toDateString(),
            'coverage_start_date' => now()->toDateString(),
            'coverage_end_date' => now()->addMonths(18)->toDateString(),
            'premium_amount' => 200,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cobra_coverages', [
            'employee_id' => $employee->id,
            'benefit_id' => $benefit->id,
            'status' => 'active',
        ]);
    }

    public function test_can_terminate_cobra_coverage(): void
    {
        $this->loginAsAdmin();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $benefit = Benefit::factory()->create(['cost' => 200]);

        $coverage = CobraCoverage::create([
            'employee_id' => $employee->id,
            'benefit_id' => $benefit->id,
            'qualifying_event_date' => now()->toDateString(),
            'coverage_start_date' => now()->toDateString(),
            'coverage_end_date' => now()->addMonths(18)->toDateString(),
            'status' => 'active',
            'premium_amount' => 200,
        ]);

        $response = $this->post(route('cobra.terminate', $coverage));

        $response->assertRedirect();
        $this->assertDatabaseHas('cobra_coverages', ['id' => $coverage->id, 'status' => 'terminated']);
    }
}
