<?php

namespace MVC\model;

require_once __DIR__ . '/../../config/database.php';

use PDO;

class Property
{
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
                p.apartment_number,
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

    public static function create(array $data): int
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            INSERT INTO properties (
                owner_id, district_name, village, block_name, plot_number, block_number, apartment_number, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['owner_id'] ?? null,
            $data['district_name'] ?? null,
            $data['village'] ?? null,
            $data['block_name'] ?? null,
            $data['plot_number'] ?? null,
            $data['block_number'] ?? null,
            $data['apartment_number'] ?? null,
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
}

