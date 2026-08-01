<?php

namespace Tests\Unit\Services;

use App\Models\Role;
use App\Models\User;
use App\Services\Auth\CustomAuthService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomAuthServiceTest extends TestCase
{
    protected CustomAuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CustomAuthService;
    }

    protected function makeUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'email' => 'auth@test.com',
            'username' => 'auth_user',
            'password' => Hash::make('password'),
            'status' => 'active',
        ], $overrides));
    }

    public function test_login_by_email_returns_user_and_sets_session(): void
    {
        $user = $this->makeUser();
        $role = Role::create(['name' => 'Employee', 'slug' => 'employee']);
        $user->roles()->attach($role);

        $result = $this->service->login('auth@test.com', 'password');

        $this->assertNotNull($result);
        $this->assertEquals($user->id, session('user_id'));
        $this->assertContains('employee', session('user_roles'));
    }

    public function test_login_with_invalid_password_returns_null(): void
    {
        $this->makeUser();

        $this->assertNull($this->service->login('auth@test.com', 'wrongpassword'));
    }

    public function test_login_with_inactive_user_returns_null(): void
    {
        $this->makeUser(['status' => 'terminated']);

        $this->assertNull($this->service->login('auth@test.com', 'password'));
    }

    public function test_login_with_unknown_identifier_returns_null(): void
    {
        $this->assertNull($this->service->login('nobody@test.com', 'password'));
    }

    public function test_account_locks_after_five_failed_attempts(): void
    {
        $user = $this->makeUser();

        for ($i = 0; $i < 5; $i++) {
            $this->service->login('auth@test.com', 'wrongpassword');
        }

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'locked']);
    }

    public function test_validate_credentials_checks_hash(): void
    {
        $user = $this->makeUser();

        $this->assertTrue($this->service->validateCredentials($user, 'password'));
        $this->assertFalse($this->service->validateCredentials($user, 'nope'));
    }

    public function test_get_current_user_from_session(): void
    {
        $user = $this->makeUser();
        $this->session(['user_id' => $user->id]);

        $current = $this->service->getCurrentUser();

        $this->assertNotNull($current);
        $this->assertEquals($user->id, $current->id);
    }

    public function test_is_authenticated_false_without_session(): void
    {
        $this->assertFalse($this->service->isAuthenticated());
    }
}
