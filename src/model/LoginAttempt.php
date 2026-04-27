<?php

namespace MVC\model;

require_once __DIR__ . '/../../config/database.php';

use PDO;

class LoginAttempt
{
    public static function record(string $ipAddress, string $username): void
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare(
            'INSERT INTO login_attempts (ip_address, username, attempted_at) VALUES (?, ?, NOW())'
        );
        $stmt->execute([$ipAddress, $username]);
    }

    public static function countRecentByIp(string $ipAddress, int $windowMinutes): int
    {
        $minutes = max(1, min($windowMinutes, 1440));
        $con = \Database::getConnection();
        $stmt = $con->prepare(
            'SELECT COUNT(*) FROM login_attempts
             WHERE ip_address = ? AND attempted_at >= DATE_SUB(NOW(), INTERVAL ' . $minutes . ' MINUTE)'
        );
        $stmt->execute([$ipAddress]);
        return (int) $stmt->fetchColumn();
    }
}
