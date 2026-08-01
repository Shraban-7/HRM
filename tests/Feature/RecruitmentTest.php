<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Interview;
use App\Models\JobPost;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RecruitmentTest extends TestCase
{
    protected function loginAsAdmin(): void
    {
        $user = User::create([
            'email' => 'recruit@test.com',
            'username' => 'recruit_admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['recruitment.view', 'recruitment.create', 'recruitment.edit', 'recruitment.delete'],
        ]);
    }

    public function test_can_create_job_post(): void
    {
        $this->loginAsAdmin();

        $response = $this->post(route('jobs.store'), [
            'title' => 'Backend Developer',
            'description' => 'Build APIs',
            'requirements' => 'PHP experience',
            'closing_date' => now()->addMonth()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('job_posts', ['title' => 'Backend Developer', 'status' => 'draft']);
    }

    public function test_can_view_job_post(): void
    {
        $this->loginAsAdmin();
        $job = JobPost::create(['title' => 'DevOps Engineer', 'description' => 'Infra role', 'status' => 'published']);

        $response = $this->get(route('jobs.show', $job));

        $response->assertStatus(200);
        $response->assertSee('DevOps Engineer');
    }

    public function test_can_create_applicant(): void
    {
        $this->loginAsAdmin();
        $job = JobPost::create(['title' => 'QA Engineer', 'description' => 'Testing role', 'status' => 'published']);

        $response = $this->post(route('applicants.store'), [
            'job_post_id' => $job->id,
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'phone' => '5550001',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('applicants', ['email' => 'alice@example.com', 'status' => 'applied']);
    }

    public function test_can_schedule_interview(): void
    {
        $this->loginAsAdmin();
        $job = JobPost::create(['title' => 'Data Engineer', 'description' => 'Data role', 'status' => 'published']);
        $applicant = Applicant::create(['job_post_id' => $job->id, 'name' => 'Bob Jones', 'email' => 'bob@example.com', 'status' => 'applied']);

        $response = $this->post(route('interviews.store'), [
            'applicant_id' => $applicant->id,
            'job_post_id' => $job->id,
            'scheduled_at' => now()->addDays(2)->toDateTimeString(),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('interviews', ['applicant_id' => $applicant->id, 'job_post_id' => $job->id]);
    }

    public function test_can_update_interview_status(): void
    {
        $this->loginAsAdmin();
        $job = JobPost::create(['title' => 'Frontend Dev', 'description' => 'UI role', 'status' => 'published']);
        $applicant = Applicant::create(['job_post_id' => $job->id, 'name' => 'Carol Lee', 'email' => 'carol@example.com', 'status' => 'applied']);
        $interview = Interview::create(['applicant_id' => $applicant->id, 'job_post_id' => $job->id, 'scheduled_at' => now()->addDays(2), 'status' => 'scheduled']);

        $response = $this->put(route('interviews.update', $interview), [
            'status' => 'completed',
            'notes' => 'Great candidate',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('interviews', ['id' => $interview->id, 'status' => 'completed']);
    }

    public function test_hire_requires_valid_department(): void
    {
        $this->loginAsAdmin();
        $job = JobPost::create(['title' => 'Intern', 'description' => 'Entry role', 'status' => 'published']);
        $applicant = Applicant::create(['job_post_id' => $job->id, 'name' => 'Dan King', 'email' => 'dan@example.com', 'status' => 'applied']);

        $response = $this->post(route('applicants.hire', $applicant), [
            'department_id' => 99999,
            'employee_id' => 'EMP-X',
            'hire_date' => '2026-08-01',
            'designation' => 'Intern',
            'salary' => 10000,
        ]);

        $response->assertSessionHasErrors('department_id');
        $this->assertDatabaseHas('applicants', ['id' => $applicant->id, 'status' => 'applied']);
    }
}
