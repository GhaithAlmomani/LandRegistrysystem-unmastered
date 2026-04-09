<?php

namespace MVC\middleware;

class AuthMiddleware {
    public const ROLE_USER = 1;
    public const ROLE_EMPLOYEE = 2;
    public const ROLE_ADMIN = 3;

    public static function requireLogin() {
        if (!isset($_SESSION['Username'])) {
            header('Location: /login');
            exit();
        }
    }

    public static function requireEmployee() {
        self::requireLogin();
        if (!isset($_SESSION['role']) || $_SESSION['role'] != self::ROLE_EMPLOYEE) {
            header('Location: /home');
            exit();
        }
    }

    public static function requireAdmin() {
        self::requireLogin();
        if (!isset($_SESSION['role']) || $_SESSION['role'] != self::ROLE_ADMIN) {
            header('Location: /home');
            exit();
        }
    }

    public static function requireUser() {
        self::requireLogin();
        if (!isset($_SESSION['role']) || $_SESSION['role'] != self::ROLE_USER) {
            header('Location: /home');
            exit();
        }
    }

    /**
     * Get a human-readable role name from an AdminID value.
     */
    public static function getRoleName(int $adminId): string {
        return match ($adminId) {
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_EMPLOYEE => 'Employee',
            default => 'Individual',
        };
    }
}