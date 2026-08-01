<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportTest extends TestCase
{
    protected function loginAsAdmin(): void
    {
        $user = User::create([
            'email' => 'reports@test.com',
            'username' => 'reports_admin',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);

        $role = Role::create(['name' => 'HR Admin', 'slug' => 'hr_admin']);
        $user->roles()->attach($role);

        $this->session([
            'user_id' => $user->id,
            'user_roles' => ['hr_admin'],
            'user_permissions' => ['audit.view'],
        ]);
    }

    public function test_can_view_report_builder(): void
    {
        $this->loginAsAdmin();

        $response = $this->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Report Builder');
    }

    public function test_can_generate_report(): void
    {
        $this->loginAsAdmin();

        $response = $this->post(route('reports.generate'), [
            'type' => 'employee-list',
            'period_start' => '2026-01-01',
            'period_end' => '2026-12-31',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
