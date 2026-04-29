<?php

namespace MVC\model;

require_once __DIR__ . '/../../config/database.php';

use PDO;

class User
{
    public static function listEmployeesByLastLogin(int $limit = 12): array
    {
        $limit = max(1, min(50, (int)$limit));
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT User_ID, User_Name, User_Email, last_login
            FROM user
            WHERE AdminID = ?
            ORDER BY last_login DESC
            LIMIT {$limit}
        ");
        $stmt->execute([\MVC\middleware\AuthMiddleware::ROLE_EMPLOYEE]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function searchAdminUsers(?string $q, string $roleFilter = 'all', string $sort = 'date_desc'): array
    {
        $con = \Database::getConnection();

        $roleMap = [
            'all' => null,
            'user' => \MVC\middleware\AuthMiddleware::ROLE_USER,
            'employee' => \MVC\middleware\AuthMiddleware::ROLE_EMPLOYEE,
            'admin' => \MVC\middleware\AuthMiddleware::ROLE_ADMIN
        ];
        $roleId = $roleMap[$roleFilter] ?? null;

        $sql = "SELECT * FROM user WHERE 1=1";
        $params = [];

        if ($roleId !== null) {
            $sql .= " AND AdminID = ?";
            $params[] = $roleId;
        }

        if ($q !== null && trim($q) !== '') {
            $term = '%' . trim($q) . '%';
            $sql .= " AND (User_Name LIKE ? OR User_Email LIKE ? OR User_ID = ? OR User_NationalID LIKE ?)";
            $params[] = $term;
            $params[] = $term;
            $params[] = is_numeric($q) ? (int)$q : -1;
            $params[] = $term;
        }

        switch ($sort) {
            case 'name_asc':
                $sql .= " ORDER BY User_Name ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY User_Name DESC";
                break;
            case 'date_asc':
                $sql .= " ORDER BY last_login ASC";
                break;
            case 'date_desc':
            default:
                $sql .= " ORDER BY last_login DESC";
                break;
        }

        $stmt = $con->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByUsername(string $username): ?array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT * FROM user WHERE User_Name = ? LIMIT 1");
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function normalizeNationalIdDigits(string $nationalId): string
    {
        return preg_replace('/\D/', '', $nationalId);
    }

    public static function normalizePhoneDigits(string $phone): string
    {
        return preg_replace('/\D/', '', $phone);
    }

    public static function normalizeFullName(string $name): string
    {
        $name = trim(preg_replace('/\s+/u', ' ', $name));
        if (function_exists('mb_strtolower')) {
            return mb_strtolower($name, 'UTF-8');
        }
        return strtolower($name);
    }

    /** Canonical display name on record (User_Name policy). */
    public static function registeredFullName(array $user): string
    {
        return trim((string)($user['User_Name'] ?? ''));
    }

    /**
     * Declared identity must match the civil record stored for this account (seller self-check or buyer lookup).
     */
    public static function validateDeclaredIdentityMatchesUser(
        array $user,
        string $declaredFullName,
        string $declaredNationalId,
        string $declaredEmail,
        string $declaredPhone
    ): ?string {
        $dbNid = self::normalizeNationalIdDigits((string)($user['User_NationalID'] ?? ''));
        $inNid = self::normalizeNationalIdDigits($declaredNationalId);
        if ($dbNid === '' || $inNid === '' || $dbNid !== $inNid) {
            return 'National ID does not match the civil registry record.';
        }

        $dbName = self::normalizeFullName(self::registeredFullName($user));
        $inName = self::normalizeFullName($declaredFullName);
        if ($dbName === '' || $inName === '' || $dbName !== $inName) {
            return 'Full name does not match the civil registry record.';
        }

        $dbEmail = strtolower(trim((string)($user['User_Email'] ?? '')));
        $inEmail = strtolower(trim($declaredEmail));
        if ($dbEmail === '' || $inEmail === '' || $dbEmail !== $inEmail) {
            return 'Email does not match the civil registry record.';
        }

        $dbPhone = self::normalizePhoneDigits((string)($user['User_Phone'] ?? ''));
        $inPhone = self::normalizePhoneDigits($declaredPhone);
        if ($dbPhone === '' || $inPhone === '' || $dbPhone !== $inPhone) {
            return 'Phone number does not match the civil registry record.';
        }

        return null;
    }

    public static function findByNationalID(string $nationalId): ?array
    {
        $digits = self::normalizeNationalIdDigits($nationalId);
        if ($digits === '') {
            return null;
        }
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT * FROM user
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(User_NationalID,' ',''),'-',''),'_',''),'+','') = ?
            LIMIT 1
        ");
        $stmt->execute([$digits]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            INSERT INTO user (User_Name, User_Email, User_Phone, User_NationalID, User_Password, AdminID, last_login)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['User_Name'] ?? null,
            $data['User_Email'] ?? null,
            $data['User_Phone'] ?? null,
            $data['User_NationalID'] ?? null,
            $data['User_Password'] ?? null,
            $data['AdminID'] ?? null,
            $data['last_login'] ?? null,
        ]);
        return (int)$con->lastInsertId();
    }

    public static function update(int $userId, array $data): bool
    {
        $allowed = [
            'User_Name',
            'User_Email',
            'User_Phone',
            'User_NationalID',
            'User_Password',
            'AdminID',
            'last_login'
        ];

        $sets = [];
        $params = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $sets[] = "{$key} = ?";
                $params[] = $data[$key];
            }
        }

        if (empty($sets)) {
            return true;
        }

        $params[] = $userId;
        $con = \Database::getConnection();
        $stmt = $con->prepare("UPDATE user SET " . implode(', ', $sets) . " WHERE User_ID = ?");
        return $stmt->execute($params);
    }

    public static function searchEmployees(?string $q, string $sort = 'date_desc'): array
    {
        $con = \Database::getConnection();

        $sql = "SELECT * FROM user WHERE AdminID = ?";
        $params = [\MVC\middleware\AuthMiddleware::ROLE_EMPLOYEE];

        if ($q !== null && trim($q) !== '') {
            $term = '%' . trim($q) . '%';
            $sql .= " AND (User_Name LIKE ? OR User_Email LIKE ? OR User_ID = ?)";
            $params[] = $term;
            $params[] = $term;
            $params[] = is_numeric($q) ? (int)$q : -1;
        }

        switch ($sort) {
            case 'name_asc':
                $sql .= " ORDER BY User_Name ASC";
                break;
            case 'name_desc':
                $sql .= " ORDER BY User_Name DESC";
                break;
            case 'date_asc':
                $sql .= " ORDER BY last_login ASC";
                break;
            case 'date_desc':
            default:
                $sql .= " ORDER BY last_login DESC";
                break;
        }

        $stmt = $con->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function existsByUsername(string $username): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT 1 FROM user WHERE User_Name = ? LIMIT 1");
        $stmt->execute([$username]);
        return (bool)$stmt->fetchColumn();
    }

    public static function existsByEmail(string $email): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT 1 FROM user WHERE User_Email = ? LIMIT 1");
        $stmt->execute([$email]);
        return (bool)$stmt->fetchColumn();
    }

    public static function existsByNationalID(string $nationalId): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT 1 FROM user WHERE User_NationalID = ? LIMIT 1");
        $stmt->execute([$nationalId]);
        return (bool)$stmt->fetchColumn();
    }

    public static function nextUserNumber(): int
    {
        $con = \Database::getConnection();
        $stmt = $con->query("SELECT MAX(User_Number) as max_num FROM user");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ((int)($row['max_num'] ?? 0)) + 1;
    }

    public static function countAll(): int
    {
        $con = \Database::getConnection();
        $stmt = $con->query("SELECT COUNT(*) FROM user");
        return (int)$stmt->fetchColumn();
    }

    public static function updateFailedLogin(string $username, int $failedLogin): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("UPDATE user SET failed_login = ? WHERE User_Name = ? LIMIT 1");
        return $stmt->execute([$failedLogin, $username]);
    }

    public static function incrementFailedLogin(string $username): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("UPDATE user SET failed_login = (failed_login + 1) WHERE User_Name = ? LIMIT 1");
        return $stmt->execute([$username]);
    }

    public static function updateLastLogin(string $username): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("UPDATE user SET last_login = NOW() WHERE User_Name = ? LIMIT 1");
        return $stmt->execute([$username]);
    }
}

