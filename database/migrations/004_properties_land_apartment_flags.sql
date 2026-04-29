-- Run once on existing databases (MySQL 8+)
USE wise;

-- Ensure "type" column exists (supports old "property_type" schema too).
SET @has_type := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'properties'
      AND COLUMN_NAME = 'type'
);

SET @has_property_type := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'properties'
      AND COLUMN_NAME = 'property_type'
);

SET @sql := IF(
    @has_type > 0,
    'SELECT 1',
    IF(
        @has_property_type > 0,
        'ALTER TABLE properties CHANGE COLUMN property_type `type` VARCHAR(50) NOT NULL DEFAULT ''land''',
        'ALTER TABLE properties ADD COLUMN `type` VARCHAR(50) NOT NULL DEFAULT ''land'' AFTER block_number'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE properties
ADD COLUMN is_land TINYINT(1) NOT NULL DEFAULT 0 AFTER `type`,
ADD COLUMN is_apartment TINYINT(1) NOT NULL DEFAULT 0 AFTER is_land;

-- Backfill flags from current type values.
UPDATE properties
SET
    is_land = CASE WHEN LOWER(COALESCE(`type`, '')) = 'land' THEN 1 ELSE 0 END,
    is_apartment = CASE WHEN LOWER(COALESCE(`type`, '')) = 'apartment' THEN 1 ELSE 0 END
WHERE id > 0;

-- If type is neither land nor apartment, keep one default active.
UPDATE properties
SET is_land = 1, is_apartment = 0
WHERE id > 0 AND is_land = 0 AND is_apartment = 0;

DROP TRIGGER IF EXISTS properties_bi_sync_flags;
DROP TRIGGER IF EXISTS properties_bu_sync_flags;

DELIMITER $$
CREATE TRIGGER properties_bi_sync_flags
BEFORE INSERT ON properties
FOR EACH ROW
BEGIN
    IF NEW.is_land = 1 THEN
        SET NEW.is_apartment = 0;
        SET NEW.`type` = 'land';
    ELSEIF NEW.is_apartment = 1 THEN
        SET NEW.is_land = 0;
        SET NEW.`type` = 'apartment';
    ELSEIF LOWER(COALESCE(NEW.`type`, '')) = 'apartment' THEN
        SET NEW.is_land = 0;
        SET NEW.is_apartment = 1;
    ELSE
        SET NEW.`type` = 'land';
        SET NEW.is_land = 1;
        SET NEW.is_apartment = 0;
    END IF;
END$$

CREATE TRIGGER properties_bu_sync_flags
BEFORE UPDATE ON properties
FOR EACH ROW
BEGIN
    IF NEW.is_land = 1 THEN
        SET NEW.is_apartment = 0;
        SET NEW.`type` = 'land';
    ELSEIF NEW.is_apartment = 1 THEN
        SET NEW.is_land = 0;
        SET NEW.`type` = 'apartment';
    ELSEIF LOWER(COALESCE(NEW.`type`, '')) = 'apartment' THEN
        SET NEW.is_land = 0;
        SET NEW.is_apartment = 1;
    ELSE
        SET NEW.`type` = 'land';
        SET NEW.is_land = 1;
        SET NEW.is_apartment = 0;
    END IF;
END$$
DELIMITER ;

