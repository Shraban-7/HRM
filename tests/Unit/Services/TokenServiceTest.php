<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\Auth\TokenService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TokenServiceTest extends TestCase
{
    protected TokenService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TokenService;
    }

    protected function makeUser(): User
    {
        return User::create([
            'email' => 'token@test.com',
            'username' => 'token_user',
            'password' => Hash::make('password'),
            'status' => 'active',
        ]);
    }

    public function test_create_reset_token_generates_hash(): void
    {
        $user = $this->makeUser();

        $reset = $this->service->createResetToken($user);

        $this->assertNotNull($reset->plain_token);
        $this->assertDatabaseHas('password_resets', ['user_id' => $user->id]);
        $this->assertNotEquals($reset->plain_token, $reset->token_hash);
    }

    public function test_validate_reset_token_returns_true_and_marks_used(): void
    {
        $user = $this->makeUser();
        $reset = $this->service->createResetToken($user);

        $valid = $this->service->validateResetToken($reset->plain_token, $user->id);

        $this->assertTrue($valid);
        $this->assertNotNull($reset->fresh()->used_at);
    }

    public function test_validate_reset_token_rejects_wrong_token(): void
    {
        $user = $this->makeUser();
        $this->service->createResetToken($user);

        $valid = $this->service->validateResetToken('wrong-token', $user->id);

        $this->assertFalse($valid);
    }
}
