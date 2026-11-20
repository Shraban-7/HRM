<?php

namespace Tests\Feature;

use App\Models\Role;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingLandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_guest_can_view_saas_marketing_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('NexusHRM');
        $response->assertSee('Super Admin');
        $response->assertSee('HR Manager');
        $response->assertSee('Starter');
        $response->assertSee('Enterprise');
    }

    public function test_interactive_demo_role_switch_super_admin(): void
    {
        $response = $this->post('/demo/switch/super_admin');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_interactive_demo_role_switch_hr_admin(): void
    {
        $response = $this->post('/demo/switch/hr_admin');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_interactive_demo_role_switch_manager(): void
    {
        $response = $this->post('/demo/switch/manager');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_interactive_demo_role_switch_employee(): void
    {
        $response = $this->post('/demo/switch/employee');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }
}
