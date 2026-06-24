<?php

namespace App\Helpers;

class EncryptionHelper
{
    public static function encrypt(string $data, string $key = null): string
    {
        $key = $key ?? config('app.key');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($iv . '::' . $encrypted);
    }

    public static function decrypt(string $data, string $key = null): string
    {
        $key = $key ?? config('app.key');
        $parts = explode('::', base64_decode($data));
        if (count($parts) !== 2) {
            return '';
        }
        [$iv, $encrypted] = $parts;
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    }
}