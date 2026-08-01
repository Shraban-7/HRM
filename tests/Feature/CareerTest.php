<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Department;
use App\Models\JobPost;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CareerTest extends TestCase
{
    public function test_career_portal_lists_published_jobs(): void
    {
        JobPost::create(['title' => 'Software Engineer', 'description' => 'Build software', 'status' => 'published']);
        JobPost::create(['title' => 'Draft Role', 'description' => 'Hidden', 'status' => 'draft']);

        $response = $this->get(route('careers.index'));

        $response->assertStatus(200);
        $response->assertSee('Software Engineer');
        $response->assertDontSee('Draft Role');
    }

    public function test_can_apply_to_job_via_career_portal(): void
    {
        $job = JobPost::create(['title' => 'Analyst', 'description' => 'Analyse data', 'status' => 'published']);

        $response = $this->post(route('careers.apply', $job), [
            'name' => 'Jane Applicant',
            'email' => 'jane@example.com',
            'phone' => '5551234',
            'resume' => UploadedFile::fake()->create('resume.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('careers.show', $job));
        $this->assertDatabaseHas('applicants', ['email' => 'jane@example.com', 'status' => 'applied']);
    }

    public function test_can_hire_applicant_as_employee(): void
    {
        $user = User::create([
            'email' => 'hr@test.com',
            'username' => 'hr_admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['recruitment.manage_applicants'],
        ]);

        $job = JobPost::create(['title' => 'Developer', 'description' => 'Write code', 'status' => 'published']);
        $applicant = Applicant::create(['job_post_id' => $job->id, 'name' => 'John Doe', 'email' => 'john@example.com', 'status' => 'applied']);
        $department = Department::create(['name' => 'Engineering']);

        $response = $this->post(route('applicants.hire', $applicant), [
            'department_id' => $department->id,
            'employee_id' => 'EMP-HIRE-001',
            'hire_date' => '2026-08-01',
            'designation' => 'Developer',
            'salary' => 60000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('applicants', ['id' => $applicant->id, 'status' => 'hired']);
        $this->assertDatabaseHas('employees', ['employee_id' => 'EMP-HIRE-001']);
    }
}
