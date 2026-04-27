<?php

namespace MVC\core;

require_once __DIR__ . '/../../config/database.php';

/**
 * AES-256-CBC at rest. IV is derived deterministically from the plaintext and key
 * so the same value always yields the same ciphertext (required for SQL equality lookups).
 */
class Encryption
{
    private static function keyBinary(): string
    {
        $key = \Database::getEnv('APP_ENCRYPTION_KEY');
        if ($key === '') {
            throw new \RuntimeException('APP_ENCRYPTION_KEY is not set in .env');
        }
        return hash('sha256', $key, true);
    }

    private static function ivForPlaintext(string $plaintext): string
    {
        $kb = self::keyBinary();
        return substr(hash_hmac('sha256', $plaintext, $kb, true), 0, 16);
    }

    public static function encrypt(string $value): string
    {
        if ($value === '') {
            return '';
        }
        $key = self::keyBinary();
        $iv = self::ivForPlaintext($value);
        $cipher = openssl_encrypt($value, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($cipher === false) {
            throw new \RuntimeException('Encryption failed');
        }
        return base64_encode($iv . $cipher);
    }

    public static function decrypt(string $value): string
    {
        if ($value === '') {
            return '';
        }
        $raw = base64_decode($value, true);
        if ($raw === false || strlen($raw) < 17) {
            return $value;
        }
        $key = self::keyBinary();
        $iv = substr($raw, 0, 16);
        $cipher = substr($raw, 16);
        $plain = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($plain === false) {
            return $value;
        }
        return $plain;
    }
}
