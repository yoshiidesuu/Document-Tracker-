<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;

class EncryptionService
{
    public function encrypt(mixed $value): string
    {
        return Crypt::encryptString(json_encode($value));
    }

    public function decrypt(string $encryptedValue): mixed
    {
        return json_decode(Crypt::decryptString($encryptedValue), true);
    }

    public function encryptField(?string $value): ?string
    {
        if ($value === null) return null;
        return $this->encrypt($value);
    }

    public function decryptField(?string $encryptedValue): ?string
    {
        if ($encryptedValue === null) return null;
        $decrypted = $this->decrypt($encryptedValue);
        return is_string($decrypted) ? $decrypted : null;
    }

    public function maskSensitiveData(string $value, int $visibleChars = 4): string
    {
        $length = strlen($value);
        if ($length <= $visibleChars) return str_repeat('*', $length);
        return substr($value, 0, $visibleChars) . str_repeat('*', $length - $visibleChars);
    }

    public function hashEmail(string $email): string
    {
        return hash_hmac('sha256', strtolower(trim($email)), Config::get('app.key'));
    }

    public function generateSecureToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}
