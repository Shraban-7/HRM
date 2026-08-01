<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Goal;
use App\Models\PerformanceReview;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    protected function loginAsAdmin(): User
    {
        $user = User::create([
            'email' => 'perf@test.com',
            'username' => 'perf_admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['performance.view', 'performance.create', 'performance.edit'],
        ]);

        return $user;
    }

    public function test_can_create_review(): void
    {
        $admin = $this->loginAsAdmin();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $response = $this->post(route('reviews.store'), [
            'employee_id' => $employee->id,
            'review_date' => now()->toDateString(),
            'rating' => 4,
            'notes' => 'Solid work',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('performance_reviews', [
            'employee_id' => $employee->id,
            'rating' => 4,
            'reviewer_id' => $admin->id,
            'status' => 'pending',
        ]);
    }

    public function test_can_view_review_analytics(): void
    {
        $this->loginAsAdmin();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        PerformanceReview::factory()->create(['employee_id' => $employee->id, 'rating' => 4]);

        $response = $this->get(route('reviews.analytics'));

        $response->assertStatus(200);
    }

    public function test_can_create_goal(): void
    {
        $this->loginAsAdmin();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $response = $this->post(route('goals.store'), [
            'employee_id' => $employee->id,
            'title' => 'Ship v2',
            'description' => 'Release major version',
            'target_date' => now()->addMonths(3)->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('goals', ['employee_id' => $employee->id, 'title' => 'Ship v2']);
    }

    public function test_can_update_goal_status(): void
    {
        $this->loginAsAdmin();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $goal = Goal::create(['employee_id' => $employee->id, 'title' => 'Learn PHP', 'status' => 'pending']);

        $response = $this->put(route('goals.update', $goal), ['status' => 'in_progress']);

        $response->assertRedirect();
        $this->assertDatabaseHas('goals', ['id' => $goal->id, 'status' => 'in_progress']);
    }

    public function test_can_submit_feedback(): void
    {
        $admin = $this->loginAsAdmin();
        $target = User::factory()->create();

        $response = $this->post(route('feedback.store'), [
            'to_user_id' => $target->id,
            'message' => 'Great collaboration',
            'category' => 'teamwork',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('feedback', [
            'to_user_id' => $target->id,
            'from_user_id' => $admin->id,
            'category' => 'teamwork',
        ]);
    }

    public function test_can_calibrate_rating(): void
    {
        $this->loginAsAdmin();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $review = PerformanceReview::factory()->create(['employee_id' => $employee->id, 'rating' => 3]);

        $response = $this->post(route('performance.calibration.update', $review), [
            'rating' => 2,
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('performance_reviews', ['id' => $review->id, 'rating' => 2, 'status' => 'completed']);
    }
}
