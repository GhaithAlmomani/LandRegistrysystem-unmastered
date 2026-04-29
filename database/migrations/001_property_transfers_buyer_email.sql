-- Run once on existing databases (MySQL 8+)
USE wise;

ALTER TABLE property_transfers
ADD COLUMN buyer_email VARCHAR(255) NOT NULL DEFAULT '' AFTER buyer_phone;
