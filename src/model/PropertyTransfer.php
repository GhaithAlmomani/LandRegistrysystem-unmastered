<?php

namespace MVC\model;

require_once __DIR__ . '/../../config/database.php';

use PDO;

class PropertyTransfer
{
    public static function findByTracking(string $trackingNumber): ?array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT * FROM property_transfers WHERE tracking_number = ? LIMIT 1");
        $stmt->execute([$trackingNumber]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function findPending(): array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT pt.*, p.district_name, p.village, p.block_name, p.plot_number, 
                   p.block_number, p.apartment_number, u.User_Name as seller_name
            FROM property_transfers pt
            JOIN properties p ON pt.property_id = p.id
            JOIN user u ON pt.seller_id = u.User_ID
            WHERE pt.status = 'pending'
            ORDER BY pt.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("INSERT INTO property_transfers (
            property_id,
            seller_id,
            buyer_name,
            buyer_national_id,
            buyer_phone,
            buyer_address,
            tracking_number,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");

        return $stmt->execute([
            $data['property_id'],
            $data['seller_id'],
            $data['buyer_name'],
            $data['buyer_national_id'],
            $data['buyer_phone'],
            $data['buyer_address'],
            $data['tracking_number'],
        ]);
    }

    public static function approve(string $trackingNumber): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("UPDATE property_transfers SET status = 'completed' WHERE tracking_number = ?");
        return $stmt->execute([$trackingNumber]);
    }

    public static function reject(string $trackingNumber): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("UPDATE property_transfers SET status = 'rejected' WHERE tracking_number = ?");
        return $stmt->execute([$trackingNumber]);
    }

    public static function setDocumentPath(string $trackingNumber, string $relativePathFromStorageRoot): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("UPDATE property_transfers SET document_path = ? WHERE tracking_number = ?");
        return $stmt->execute([$relativePathFromStorageRoot, $trackingNumber]);
    }

    public static function getDocumentPath(string $trackingNumber): ?string
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT document_path FROM property_transfers WHERE tracking_number = ? AND status = 'completed' LIMIT 1");
        $stmt->execute([$trackingNumber]);
        $path = $stmt->fetchColumn();
        return $path !== false ? (string)$path : null;
    }

    public static function findTransferDetailsForDocument(string $trackingNumber): ?array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT pt.*, p.*, u.User_Name, u.User_NationalID, u.User_Phone, u.User_Email
            FROM property_transfers pt
            JOIN properties p ON pt.property_id = p.id
            JOIN user u ON pt.seller_id = u.User_ID
            WHERE pt.tracking_number = ?
            LIMIT 1
        ");
        $stmt->execute([$trackingNumber]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function countForUser(int $sellerId, string $buyerNationalId): int
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT COUNT(*)
            FROM property_transfers pt
            WHERE pt.seller_id = ? OR pt.buyer_national_id = ?
        ");
        $stmt->execute([$sellerId, $buyerNationalId]);
        return (int)$stmt->fetchColumn();
    }

    public static function findOrdersForUser(int $userId, string $userNationalId): array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT 
                pt.*,
                CONCAT(p.district_name, ', ', p.village) as property_name,
                CONCAT(p.block_name, ' Block ', p.block_number, ', Plot ', p.plot_number, 
                       CASE WHEN p.apartment_number IS NOT NULL THEN CONCAT(', Apt ', p.apartment_number) ELSE '' END) as property_location,
                CASE 
                    WHEN pt.seller_id = ? THEN 'seller'
                    WHEN u.User_NationalID = pt.buyer_national_id THEN 'buyer'
                    ELSE 'unknown'
                END as user_role
            FROM property_transfers pt
            JOIN properties p ON pt.property_id = p.id
            LEFT JOIN user u ON u.User_NationalID = pt.buyer_national_id
            WHERE pt.seller_id = ? OR pt.buyer_national_id = ?
            ORDER BY pt.created_at DESC
        ");
        $stmt->execute([$userId, $userId, $userNationalId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countAll(): int
    {
        $con = \Database::getConnection();
        $stmt = $con->query("SELECT COUNT(*) FROM property_transfers");
        return (int)$stmt->fetchColumn();
    }
}

