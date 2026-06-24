<?php

namespace App\Helpers;

class HashHelper
{
    public static function hashPassword(string $password, string $salt): string
    {
        $pepper = config('hrm.pepper', '');
        return hash('sha256', $salt . $password . $pepper);
    }

    public static function verifyPassword(string $password, string $salt, string $hash): bool
    {
        return self::hashPassword($password, $salt) === $hash;
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