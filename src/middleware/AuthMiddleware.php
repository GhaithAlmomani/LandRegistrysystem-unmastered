<?php

namespace MVC\middleware;

class AuthMiddleware {
    public static function requireLogin() {
        if (!isset($_SESSION['Username'])) {
            header('Location: /login');
            exit();
        }
    }

    public static function requireEmployee() {
        self::requireLogin();
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 2) {
            header('Location: /home');
            exit();
        }
    }

    public static function requireAdmin() {
        self::requireLogin();
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 3) {
            header('Location: /home');
            exit();
        }
    }

    public static function requireUser() {
        self::requireLogin();
        if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
            header('Location: /home');
            exit();
        }
    }
} 