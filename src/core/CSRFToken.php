<?php

namespace MVC\core;

class CSRFToken
{
    private const string TOKEN_NAME = 'csrf_token';
    private const int TOKEN_LENGTH = 32;
    private const int TOKEN_EXPIRY = 7200; // 2 hours in seconds

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->regenerateTokenIfNeeded();
    }

    private function regenerateTokenIfNeeded(): void
    {
        if (empty($_SESSION[self::TOKEN_NAME]) || 
            empty($_SESSION['csrf_token_time']) || 
            (time() - $_SESSION['csrf_token_time']) > self::TOKEN_EXPIRY) {
            $this->regenerateToken();
        }
    }

    public function regenerateToken(): void
    {
        $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $_SESSION['csrf_token_time'] = time();
    }

    public static function getToken(): string
    {
        if (empty($_SESSION[self::TOKEN_NAME])) {
            (new self())->regenerateToken();
        }
        return $_SESSION[self::TOKEN_NAME] ?? '';
    }

    public static function validateToken(?string $token): bool
    {
        if (empty($token) || empty($_SESSION[self::TOKEN_NAME])) {
            return false;
        }

        // Check if token has expired
        if (empty($_SESSION['csrf_token_time']) || 
            (time() - $_SESSION['csrf_token_time']) > self::TOKEN_EXPIRY) {
            return false;
        }

        return hash_equals($_SESSION[self::TOKEN_NAME], $token);
    }

    public static function generateFormField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::getToken()) . '">';
    }

    public static function getTokenHeader(): string
    {
        return 'X-CSRF-TOKEN: ' . self::getToken();
    }

    public static function validateRequest(): bool
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        return self::validateToken($token);
    }
} 