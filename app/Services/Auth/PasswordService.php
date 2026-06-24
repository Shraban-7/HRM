<?php

namespace App\Services\Auth;

use App\Helpers\HashHelper;

class PasswordService
{
    public function hashPassword(string $password, string $salt): string
    {
        return HashHelper::hashPassword($password, $salt);
    }

    public function verifyPassword(string $password, string $salt, string $hash): bool
    {
        return HashHelper::verifyPassword($password, $salt, $hash);
    }

    public function generateSalt(): string
    {
        return HashHelper::generateSalt();
    }

    public function generatePepper(): string
    {
        return bin2hex(random_bytes(32));
    }
}