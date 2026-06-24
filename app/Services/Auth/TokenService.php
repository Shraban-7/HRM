<?php

namespace App\Services\Auth;

use App\Models\PasswordReset;
use App\Models\User;
use App\Helpers\HashHelper;
use Carbon\Carbon;

class TokenService
{
    public function createResetToken(User $user): PasswordReset
    {
        $token = HashHelper::generateToken();
        $hash = hash('sha256', $token);

        $passwordReset = PasswordReset::create([
            'user_id' => $user->id,
            'token_hash' => $hash,
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        $passwordReset->plain_token = $token;

        return $passwordReset;
    }

    public function validateResetToken(string $token, int $userId): bool
    {
        $hash = hash('sha256', $token);

        $reset = PasswordReset::where('user_id', $userId)
            ->where('token_hash', $hash)
            ->whereNull('used_at')
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$reset) {
            return false;
        }

        $reset->update(['used_at' => Carbon::now()]);

        return true;
    }
}