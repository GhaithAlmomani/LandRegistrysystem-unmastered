<?php

namespace MVC\core\session;

use Exception;
use InvalidArgumentException;
use MVC\core\Hash;
use RuntimeException;

class Session
{
    private const string CONFIG_SESSION_PATH    = 'MVC.session_path';
    private const string CONFIG_SESSION_PREFIX  = 'MVC.session_prefix';
    private const string CONFIG_SESSION_TIMEOUT = 'MVC.session_timeout';

    public function __construct()
    {
        // Initialize custom session handler
        $handler = new SessionHandler(
            config(self::CONFIG_SESSION_PATH),
            config(self::CONFIG_SESSION_PREFIX)
        );
        session_set_save_handler($handler, true);

        // Configure session settings
        session_name(config(self::CONFIG_SESSION_PREFIX));
        if (config(self::CONFIG_SESSION_PATH)) {
            session_save_path(config(self::CONFIG_SESSION_PATH));
        }

        // Start the session if not already active
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start([
                'cookie_lifetime' => config(self::CONFIG_SESSION_TIMEOUT),
            ]);
        }
    }
    private static function validateKey(string $key): void
    {
        if (empty($key) || !preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
            throw new InvalidArgumentException("Invalid session key.");
        }
    }
    public static function get(string $key): ?string
    {
        self::validateKey($key);

        if (!isset($_SESSION[$key])) {
            return null;
        }

        try {
            return Hash::decrypt($_SESSION[$key]);
        } catch (Exception $e) {
            error_log("Failed to decrypt session key '$key': " . $e->getMessage());
            return null;
        }
    }
    public static function make(string $key, mixed $value = null): ?string
    {
        self::validateKey($key);

        if (!is_null($value)) {
            try {
                $_SESSION[$key] = Hash::encrypt($value);
            } catch (Exception $e) {
                error_log("Failed to encrypt session key '$key': " . $e->getMessage());
                throw new RuntimeException("Failed to store session data.");
            }
        }

        return self::get($key);
    }
    public static function has(string $key): bool
    {
        self::validateKey($key);
        return isset($_SESSION[$key]);
    }
    public static function flash(string $key, mixed $value = null): ?string
    {
        self::validateKey($key);

        if (!is_null($value)) {
            try {
                $_SESSION[$key] = Hash::encrypt($value);
            } catch (Exception $e) {
                error_log("Failed to encrypt session key '$key': " . $e->getMessage());
                throw new RuntimeException("Failed to store session flash data.");
            }
        }

        $session = self::get($key);
        self::forget($key);
        return $session;
    }
    public static function forget(string $key): void
    {
        self::validateKey($key);
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    public static function forget_all(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            session_destroy();
        }
    }
}