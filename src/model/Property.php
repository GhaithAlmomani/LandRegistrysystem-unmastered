<?php

namespace MVC\model;

require_once __DIR__ . '/../../config/database.php';

use PDO;

class Property
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PENDING_TRANSFER = 'pending_transfer';
    public const STATUS_TRANSFERRED = 'transferred';

    public static function findByOwner(int $ownerId): array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT 
                p.id,
                u.User_Name as owner_name,
                p.district_name,
                p.village,
                p.block_name,
                p.plot_number,
                p.block_number,
                p.`type`,
                p.area,
                p.status,
                p.created_at
            FROM properties p
            JOIN user u ON p.owner_id = u.User_ID
            WHERE p.owner_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findById(int $propertyId): ?array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT * FROM properties WHERE id = ? LIMIT 1");
        $stmt->execute([$propertyId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function findByIdAndOwner(int $propertyId, int $ownerId): ?array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT * FROM properties WHERE id = ? AND owner_id = ? LIMIT 1");
        $stmt->execute([$propertyId, $ownerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function countAll(): int
    {
        $con = \Database::getConnection();
        $stmt = $con->query("SELECT COUNT(*) FROM properties");
        return (int)$stmt->fetchColumn();
    }

    public static function countByStatus(string $status): int
    {
        $allowed = [
            self::STATUS_ACTIVE,
            self::STATUS_PENDING_TRANSFER,
            self::STATUS_TRANSFERRED
        ];
        if (!in_array($status, $allowed, true)) {
            return 0;
        }

        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT COUNT(*) FROM properties WHERE status = ?");
        $stmt->execute([$status]);
        return (int)$stmt->fetchColumn();
    }

    public static function create(array $data): int
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            INSERT INTO properties (
                owner_id, district_name, village, block_name, plot_number, block_number, `type`, area, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['owner_id'] ?? null,
            $data['district_name'] ?? null,
            $data['village'] ?? null,
            $data['block_name'] ?? null,
            $data['plot_number'] ?? null,
            $data['block_number'] ?? null,
            $data['type'] ?? 'land',
            $data['area'] ?? null,
            $data['status'] ?? 'active',
            $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
        return (int)$con->lastInsertId();
    }

    public static function updateOwner(int $propertyId, int $newOwnerId): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("UPDATE properties SET owner_id = ? WHERE id = ?");
        return $stmt->execute([$newOwnerId, $propertyId]);
    }

    public static function updateStatus(int $propertyId, string $status, ?int $ownerId = null): bool
    {
        $con = \Database::getConnection();
        if ($ownerId !== null) {
            $stmt = $con->prepare("UPDATE properties SET status = ? WHERE id = ? AND owner_id = ?");
            return $stmt->execute([$status, $propertyId, $ownerId]);
        }
        $stmt = $con->prepare("UPDATE properties SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $propertyId]);
    }

    public static function search(array $criteria): array
    {
        $con = \Database::getConnection();

        $sql = "SELECT * FROM properties WHERE 1=1";
        $params = [];

        if (!empty($criteria['owner_id'])) {
            $sql .= " AND owner_id = ?";
            $params[] = (int)$criteria['owner_id'];
        }
        if (!empty($criteria['status'])) {
            $sql .= " AND status = ?";
            $params[] = $criteria['status'];
        }
        if (!empty($criteria['district_name'])) {
            $sql .= " AND district_name LIKE ?";
            $params[] = '%' . $criteria['district_name'] . '%';
        }
        if (!empty($criteria['village'])) {
            $sql .= " AND village LIKE ?";
            $params[] = '%' . $criteria['village'] . '%';
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $con->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function searchSystemRecords(?string $q, int $limit = 50): array
    {
        $limit = max(1, min(200, (int)$limit));
        $con = \Database::getConnection();

        $sql = "
            SELECT
                p.*,
                u.User_Name as owner_name,
                u.User_NationalID as owner_national_id
            FROM properties p
            JOIN user u ON p.owner_id = u.User_ID
            WHERE 1=1
        ";
        $params = [];

        if ($q !== null && trim($q) !== '') {
            $term = '%' . trim($q) . '%';
            $sql .= " AND (
                p.id = ?
                OR p.district_name LIKE ?
                OR p.village LIKE ?
                OR p.block_name LIKE ?
                OR p.plot_number LIKE ?
                OR p.block_number LIKE ?
                OR p.`type` LIKE ?
                OR CAST(p.area AS CHAR) LIKE ?
                OR u.User_Name LIKE ?
                OR u.User_NationalID LIKE ?
            )";
            $params[] = is_numeric($q) ? (int)$q : -1;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT {$limit}";

        $stmt = $con->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

