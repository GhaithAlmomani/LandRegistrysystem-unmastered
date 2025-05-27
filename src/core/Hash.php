<?php

namespace MVC\core;

use Exception;
use RuntimeException;

class Hash
{
    private const string CONFIG_ENCRYPTION_MODE = 'MVC.encryption_mode';
    private const string CONFIG_ENCRYPTION_KEY  = 'MVC.encryption_key';
    private const string CONFIG_BCRYPT_ALGO     = 'MVC.bcrypt_algo';
    public static function encrypt(string $value): string
    {
        try {
            $cipher = config(self::CONFIG_ENCRYPTION_MODE);
            $key    = config(self::CONFIG_ENCRYPTION_KEY);

            $ivLen  = openssl_cipher_iv_length($cipher);
            if ($ivLen === false || $ivLen <= 0) {
                throw new RuntimeException('Failed to get IV length for cipher.');
            }

            $iv = openssl_random_pseudo_bytes($ivLen);
            if (!$iv) {
                throw new RuntimeException('Failed to generate IV.');
            }

            $ciphertext_raw = openssl_encrypt($value, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            if ($ciphertext_raw === false) {
                throw new RuntimeException('Encryption failed.');
            }

            $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
            return base64_encode($iv . $hmac . $ciphertext_raw);
        } catch (Exception $e) {
            throw new RuntimeException('Encryption error: ' . $e->getMessage());
        }
    }
    public static function decrypt(string $cipherText): string
    {
        try {
            $cipher = config(self::CONFIG_ENCRYPTION_MODE);
            $key = config(self::CONFIG_ENCRYPTION_KEY);
            $convert = base64_decode($cipherText);

            if ($convert === false) {
                throw new RuntimeException('Base64 decoding failed.');
            }

            $ivLen = openssl_cipher_iv_length($cipher);

            if ($ivLen === false) {
                throw new RuntimeException('Failed to get IV length for cipher.');
            }

            $iv = substr($convert, 0, $ivLen);
            $hmac = substr($convert, $ivLen, 32);
            $ciphertext_raw = substr($convert, $ivLen + 32);

            $original_text = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            if ($original_text === false) {
                throw new RuntimeException('Decryption failed.');
            }

            $calcMac = hash_hmac('sha256', $ciphertext_raw, $key, true);
            if (!hash_equals($hmac, $calcMac)) {
                throw new RuntimeException('HMAC verification failed.');
            }

            return $original_text;
        } catch (Exception $e) {
            throw new RuntimeException('Decryption error: ' . $e->getMessage());
        }
    }
    public static function make(string $password): string
    {
        return password_hash($password, config(self::CONFIG_BCRYPT_ALGO));
    }
    public static function check(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}