<?php

namespace MVC\model;

require_once __DIR__ . '/../../config/database.php';

use PDO;

class PropertyTransfer
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    public static function findByTracking(string $trackingNumber): ?array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT * FROM property_transfers WHERE tracking_number = ? LIMIT 1");
        $stmt->execute([$trackingNumber]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function findByTrackingAndSeller(string $trackingNumber, int $sellerId): ?array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT *
            FROM property_transfers
            WHERE tracking_number = ? AND seller_id = ?
            LIMIT 1
        ");
        $stmt->execute([$trackingNumber, $sellerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function findPending(): array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT pt.*, p.district_name, p.village, p.block_name, p.plot_number,
                   p.block_number, p.`type`, p.area, u.User_Name as seller_name
            FROM property_transfers pt
            JOIN properties p ON pt.property_id = p.id
            JOIN user u ON pt.seller_id = u.User_ID
            WHERE pt.status = 'pending'
            ORDER BY pt.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findPendingByProperty(int $propertyId): ?array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT *
            FROM property_transfers
            WHERE property_id = ? AND status = 'pending'
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$propertyId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function findPendingByPropertyAndSeller(int $propertyId, int $sellerId): ?array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT *
            FROM property_transfers
            WHERE property_id = ? AND seller_id = ? AND status = 'pending'
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$propertyId, $sellerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
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
            buyer_email,
            buyer_address,
            tracking_number,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");

        return $stmt->execute([
            $data['property_id'],
            $data['seller_id'],
            $data['buyer_name'],
            $data['buyer_national_id'],
            $data['buyer_phone'],
            $data['buyer_email'] ?? '',
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

    public static function findTransferDetailsForAdmin(string $trackingNumber): ?array
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT
                pt.*,
                p.*,
                seller.User_Name as seller_name,
                seller.User_NationalID as seller_national_id,
                seller.User_Phone as seller_phone,
                seller.User_Email as seller_email,
                buyer.User_ID as buyer_user_id,
                buyer.User_Name as buyer_user_name,
                buyer.User_NationalID as buyer_user_national_id,
                buyer.User_Phone as buyer_user_phone,
                buyer.User_Email as buyer_user_email
            FROM property_transfers pt
            JOIN properties p ON pt.property_id = p.id
            JOIN user seller ON pt.seller_id = seller.User_ID
            LEFT JOIN user buyer ON buyer.User_NationalID = pt.buyer_national_id
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
                CONCAT(
                    p.block_name,
                    ' Block ',
                    p.block_number,
                    ', Plot ',
                    p.plot_number,
                    ', ',
                    UPPER(LEFT(COALESCE(p.`type`, 'land'), 1)),
                    SUBSTRING(COALESCE(p.`type`, 'land'), 2),
                    CASE WHEN p.area IS NOT NULL THEN CONCAT(', ', p.area, ' m²') ELSE '' END
                ) as property_location,
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

    public static function countByStatus(string $status): int
    {
        $allowed = [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_COMPLETED
        ];
        if (!in_array($status, $allowed, true)) {
            return 0;
        }

        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT COUNT(*) FROM property_transfers WHERE status = ?");
        $stmt->execute([$status]);
        return (int)$stmt->fetchColumn();
    }

    public static function countByStatusSince(string $status, string $sinceDateTime, string $timeColumn = 'updated_at'): int
    {
        $allowedStatus = [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_COMPLETED
        ];
        if (!in_array($status, $allowedStatus, true)) {
            return 0;
        }

        $allowedColumns = ['created_at', 'updated_at'];
        if (!in_array($timeColumn, $allowedColumns, true)) {
            $timeColumn = 'updated_at';
        }

        $con = \Database::getConnection();
        $stmt = $con->prepare("SELECT COUNT(*) FROM property_transfers WHERE status = ? AND {$timeColumn} >= ?");
        $stmt->execute([$status, $sinceDateTime]);
        return (int)$stmt->fetchColumn();
    }

    public static function findPendingLimited(int $limit = 10): array
    {
        $limit = max(1, min(50, (int)$limit));
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT pt.*, p.district_name, p.village, p.block_name, p.plot_number,
                   p.block_number, p.`type`, p.area, u.User_Name as seller_name
            FROM property_transfers pt
            JOIN properties p ON pt.property_id = p.id
            JOIN user u ON pt.seller_id = u.User_ID
            WHERE pt.status = 'pending'
            ORDER BY pt.created_at DESC
            LIMIT {$limit}
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findForSeller(int $sellerId, int $limit = 100): array
    {
        $limit = max(1, min(200, (int)$limit));
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT
                pt.*,
                p.district_name,
                p.village,
                p.block_name,
                p.plot_number,
                p.block_number,
                p.`type`,
                p.area
            FROM property_transfers pt
            JOIN properties p ON pt.property_id = p.id
            WHERE pt.seller_id = ?
            ORDER BY pt.created_at DESC
            LIMIT {$limit}
        ");
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function cancelBySellerWithin24Hours(string $trackingNumber, int $sellerId): bool
    {
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            UPDATE property_transfers
            SET
                status = 'rejected',
                cancelled_by_seller = 1,
                cancelled_at = NOW(),
                updated_at = NOW()
            WHERE
                tracking_number = ?
                AND seller_id = ?
                AND status = 'pending'
                AND created_at >= (NOW() - INTERVAL 24 HOUR)
                AND (cancelled_by_seller = 0 OR cancelled_by_seller IS NULL)
            LIMIT 1
        ");
        $stmt->execute([$trackingNumber, $sellerId]);
        return $stmt->rowCount() > 0;
    }

    public static function countStuckPending(int $days = 7): int
    {
        $days = max(1, min(365, (int)$days));
        $con = \Database::getConnection();
        $stmt = $con->prepare("
            SELECT COUNT(*)
            FROM property_transfers
            WHERE status = 'pending' AND created_at < (NOW() - INTERVAL {$days} DAY)
        ");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public static function reportTransfers(?string $fromDate, ?string $toDate, ?string $status): array
    {
        $allowedStatus = [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_COMPLETED
        ];

        $params = [];
        $where = [];

        if ($fromDate !== null && $fromDate !== '') {
            $where[] = "pt.created_at >= ?";
            $params[] = $fromDate . " 00:00:00";
        }
        if ($toDate !== null && $toDate !== '') {
            $where[] = "pt.created_at <= ?";
            $params[] = $toDate . " 23:59:59";
        }
        if ($status !== null && $status !== '' && in_array($status, $allowedStatus, true)) {
            $where[] = "pt.status = ?";
            $params[] = $status;
        }

        $sql = "
            SELECT
                pt.tracking_number,
                pt.status,
                pt.created_at,
                pt.updated_at,
                pt.buyer_name,
                pt.buyer_national_id,
                pt.buyer_phone,
                pt.buyer_address,
                u.User_Name as seller_name,
                u.User_NationalID as seller_national_id,
                p.id as property_id,
                p.district_name,
                p.village,
                p.block_name,
                p.plot_number,
                p.block_number,
                p.`type`,
                p.area
            FROM property_transfers pt
            JOIN properties p ON pt.property_id = p.id
            JOIN user u ON pt.seller_id = u.User_ID
        ";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY pt.created_at DESC";

        $con = \Database::getConnection();
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
                pt.tracking_number,
                pt.status,
                pt.created_at,
                pt.updated_at,
                pt.buyer_name,
                pt.buyer_national_id,
                seller.User_Name as seller_name,
                p.id as property_id,
                p.district_name,
                p.village,
                p.block_name,
                p.plot_number,
                p.block_number,
                p.`type`,
                p.area
            FROM property_transfers pt
            JOIN properties p ON pt.property_id = p.id
            JOIN user seller ON pt.seller_id = seller.User_ID
            WHERE 1=1
        ";
        $params = [];

        if ($q !== null && trim($q) !== '') {
            $term = '%' . trim($q) . '%';
            $sql .= " AND (
                pt.tracking_number LIKE ?
                OR pt.buyer_name LIKE ?
                OR pt.buyer_national_id LIKE ?
                OR seller.User_Name LIKE ?
                OR p.id = ?
                OR p.district_name LIKE ?
                OR p.village LIKE ?
                OR p.block_name LIKE ?
                OR p.plot_number LIKE ?
                OR p.block_number LIKE ?
                OR p.`type` LIKE ?
                OR CAST(p.area AS CHAR) LIKE ?
            )";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = is_numeric($q) ? (int)$q : -1;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY pt.created_at DESC LIMIT {$limit}";

        $stmt = $con->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

