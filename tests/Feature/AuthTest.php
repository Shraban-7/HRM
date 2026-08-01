<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    protected function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => Hash::make('password'),
            'status' => 'active',
        ], $overrides));
    }

    public function test_user_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = $this->createUser();

        $response = $this->post('/login', [
            'identifier' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('user_id', $user->id);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = $this->createUser();

        $response = $this->post('/login', [
            'identifier' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = $this->createUser();
        $this->withSession(['user_id' => $user->id]);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'email' => 'newuser@example.com',
            'username' => 'newuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('auth.login'));
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'username' => 'newuser',
            'status' => 'active',
        ]);
    }

    public function test_middleware_blocks_routes_without_permission(): void
    {
        $user = $this->createUser();
        $this->withSession(['user_id' => $user->id, 'user_roles' => [], 'user_permissions' => []]);

        $response = $this->get('/employees');

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }
}
