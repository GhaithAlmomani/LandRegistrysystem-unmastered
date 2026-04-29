-- Run once on existing databases (MySQL 8+)
USE wise;

ALTER TABLE properties
ADD COLUMN type VARCHAR(50) NOT NULL DEFAULT 'land' AFTER block_number,
ADD COLUMN area DECIMAL(12,2) DEFAULT NULL AFTER type;

-- Optional backfill from legacy apartment_number column if present
UPDATE properties
SET type = CASE
    WHEN apartment_number IS NULL OR apartment_number = '' OR apartment_number = '-' THEN 'land'
    ELSE 'apartment'
END
WHERE id > 0
  AND (type IS NULL OR type = '');

