<?php

namespace Tests\Feature;

use App\Models\PerformanceFormTemplate;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PerformanceTemplateTest extends TestCase
{
    protected function loginAsAdmin(): void
    {
        $user = User::create([
            'email' => 'perf-template@test.com',
            'username' => 'perf_admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['performance.manage'],
        ]);
    }

    public function test_can_list_templates(): void
    {
        $this->loginAsAdmin();
        PerformanceFormTemplate::create([
            'name' => 'Quarterly Review',
            'slug' => 'quarterly-review',
            'description' => 'Standard quarterly form',
            'questions' => ['Technical competence', 'Teamwork'],
            'is_active' => true,
        ]);

        $response = $this->get(route('performance.templates.index'));

        $response->assertStatus(200);
        $response->assertSee('Quarterly Review');
    }

    public function test_can_create_template(): void
    {
        $this->loginAsAdmin();

        $response = $this->post(route('performance.templates.store'), [
            'name' => 'Annual Review',
            'description' => 'Annual form',
            'questions' => ['Goal achievement', 'Communication'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('performance_form_templates', ['name' => 'Annual Review', 'is_active' => true]);
    }

    public function test_can_delete_template(): void
    {
        $this->loginAsAdmin();
        $template = PerformanceFormTemplate::create([
            'name' => 'Temporary Form',
            'slug' => 'temporary-form',
            'description' => null,
            'questions' => ['Question'],
            'is_active' => true,
        ]);

        $response = $this->delete(route('performance.templates.destroy', $template));

        $response->assertRedirect();
        $this->assertDatabaseMissing('performance_form_templates', ['id' => $template->id]);
    }
}
