<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;

class HashHelper
{
    public static function hashPassword(string $password, string $salt = ''): string
    {
        return Hash::make($password);
    }

    public static function verifyPassword(string $password, string $salt, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    public static function generateSalt(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    public static function generateToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length));
    }
}
