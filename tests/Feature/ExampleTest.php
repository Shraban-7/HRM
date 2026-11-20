<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that root landing page renders for guests.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // Root route returns the SaaS marketing landing page
        $response->assertStatus(200);
        $response->assertSee('NexusHRM');
    }

    /**
     * Test that root redirects authenticated user to dashboard.
     */
    public function test_authenticated_user_redirects_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('dashboard'));
    }
}
