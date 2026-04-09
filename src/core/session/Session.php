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
    private const string SESSION_METADATA_KEY   = '_session_metadata';
    private const string SESSION_ACTIVITY_KEY   = '_session_activity';

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

        // Initialize session metadata if not exists
        if (!isset($_SESSION[self::SESSION_METADATA_KEY])) {
            $this->initializeSessionMetadata();
        }

        // Update session activity
        $this->updateSessionActivity();
    }

    private function initializeSessionMetadata(): void
    {
        $_SESSION[self::SESSION_METADATA_KEY] = [
            'created_at' => time(),
            'last_activity' => time(),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'login_count' => 0,
            'last_login' => null,
            'session_id' => session_id(),
            'is_mobile' => $this->isMobileDevice(),
            'browser' => $this->getBrowserInfo(),
            'platform' => $this->getPlatformInfo()
        ];
    }

    private function updateSessionActivity(): void
    {
        if (!isset($_SESSION[self::SESSION_ACTIVITY_KEY])) {
            $_SESSION[self::SESSION_ACTIVITY_KEY] = [];
        }

        $currentPage = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $currentTime = time();

        // Keep only last 10 activities
        $_SESSION[self::SESSION_ACTIVITY_KEY] = array_slice(
            array_merge(
                [['page' => $currentPage, 'time' => $currentTime]],
                $_SESSION[self::SESSION_ACTIVITY_KEY]
            ),
            0,
            10
        );

        // Update last activity in metadata
        $_SESSION[self::SESSION_METADATA_KEY]['last_activity'] = $currentTime;
    }

    private function isMobileDevice(): bool
    {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"] ?? '');
    }

    private function getBrowserInfo(): array
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $browser = 'Unknown';
        $version = 'Unknown';

        if (preg_match('/MSIE/i', $userAgent)) {
            $browser = 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Opera/i', $userAgent)) {
            $browser = 'Opera';
        }

        return ['name' => $browser, 'version' => $version];
    }

    private function getPlatformInfo(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        if (preg_match('/windows|win32/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            return 'Mac';
        } elseif (preg_match('/linux/i', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            return 'iOS';
        }
        
        return 'Unknown';
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

    public static function getMetadata(): ?array
    {
        return $_SESSION[self::SESSION_METADATA_KEY] ?? null;
    }

    public static function getActivity(): ?array
    {
        return $_SESSION[self::SESSION_ACTIVITY_KEY] ?? null;
    }

    public static function updateLoginCount(): void
    {
        if (isset($_SESSION[self::SESSION_METADATA_KEY])) {
            $_SESSION[self::SESSION_METADATA_KEY]['login_count']++;
            $_SESSION[self::SESSION_METADATA_KEY]['last_login'] = time();
        }
    }
}