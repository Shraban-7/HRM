<?php

namespace App\Helpers;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class EncryptionHelper
{
    public static function encrypt(string $data, ?string $key = null): string
    {
        return Crypt::encryptString($data);
    }

    public static function decrypt(string $data, ?string $key = null): string
    {
        try {
            return Crypt::decryptString($data);
        } catch (DecryptException $e) {
            throw new \RuntimeException('Invalid encrypted payload.', 0, $e);
        }
    }
}
