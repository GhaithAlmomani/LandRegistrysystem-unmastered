<?php

namespace MVC\core;

class CSRFToken
{
    private const string TOKEN_NAME = 'csrf_token';
    private const int TOKEN_LENGTH = 32;

    public function __construct()
    {
        if (empty($_SESSION[self::TOKEN_NAME])) {
            $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(self::TOKEN_LENGTH));
        }
    }

    public static function getToken(): string
    {
        return $_SESSION[self::TOKEN_NAME] ?? '';
    }

    public static function validateToken(?string $token): bool
    {
        if (empty($token) || empty($_SESSION[self::TOKEN_NAME])) {
            return false;
        }
        return hash_equals($_SESSION[self::TOKEN_NAME], $token);
    }

    public static function generateFormField(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::getToken() . '">';
    }
} 