-- Run once on existing databases (MySQL 8+)
USE wise;

ALTER TABLE property_transfers
ADD COLUMN cancelled_by_seller TINYINT(1) NOT NULL DEFAULT 0 AFTER status,
ADD COLUMN cancelled_at TIMESTAMP NULL DEFAULT NULL AFTER cancelled_by_seller;

