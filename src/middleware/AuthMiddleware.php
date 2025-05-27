<?php

namespace MVC\middleware;

class AuthMiddleware {
    public static function requireAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['Username'])) {
            header('Location: login');
            exit();
        }
    }

    public static function requireRole($requiredRole) {
        self::requireAuth();
        
        if (!isset($_SESSION['role']) || $_SESSION['role'] != $requiredRole) {
            header('Location: home');
            exit();
        }
    }

    public static function requireAdmin() {
        self::requireRole(3);
    }

    public static function requireEmployee() {
        self::requireRole(2);
    }

    public static function requireUser() {
        self::requireRole(1);
    }
} 