<?php

namespace Tests\Feature;

use App\Models\User;
use App\Helpers\HashHelper;
use App\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    protected function createUser(array $overrides = []): User
    {
        $salt = HashHelper::generateSalt();
        return User::create(array_merge([
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password_hash' => HashHelper::hashPassword('password', $salt),
            'salt' => $salt,
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
        $this->assertSessionHas('user_id', $user->id);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = $this->createUser();

        $response = $this->post('/login', [
            'identifier' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $this->assertSessionHasErrors();
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
}