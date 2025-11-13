-- ============================================================================
-- SAFE SCHEMA FIX FOR RENTIGO DATABASE
-- This script checks if columns exist before adding them
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- FIX PROPERTIES TABLE
-- ============================================================================

-- Add approval_status column if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = 'properties';
SET @columnname = 'approval_status';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending' AFTER status")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add approved_at column if it doesn't exist
SET @columnname = 'approved_at';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " timestamp NULL DEFAULT NULL AFTER approval_status")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Update existing records to sync status with approval_status
UPDATE `properties` SET `approval_status` =
    CASE
        WHEN `status` IN ('pending') THEN 'pending'
        WHEN `status` IN ('approved', 'available', 'occupied', 'maintenance') THEN 'approved'
        WHEN `status` = 'rejected' THEN 'rejected'
        ELSE 'pending'
    END
WHERE `approval_status` IS NULL OR `approval_status` = '';

-- ============================================================================
-- FIX PROPERTY_MANAGER TABLE
-- ============================================================================

-- Update 'verified' to 'approved' before changing column type
UPDATE `property_manager` SET `verification_status` = 'approved' WHERE `verification_status` = 'verified';

-- Check and rename document_filename to employee_id_filename
SET @tablename = 'property_manager';
SET @oldcolumn = 'document_filename';
SET @newcolumn = 'employee_id_filename';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @oldcolumn)
  ) > 0,
  CONCAT("ALTER TABLE ", @tablename, " CHANGE COLUMN ", @oldcolumn, " ", @newcolumn, " varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL"),
  "SELECT 1"
));
PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

-- Check and rename document_mimetype to employee_id_filetype
SET @oldcolumn = 'document_mimetype';
SET @newcolumn = 'employee_id_filetype';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @oldcolumn)
  ) > 0,
  CONCAT("ALTER TABLE ", @tablename, " CHANGE COLUMN ", @oldcolumn, " ", @newcolumn, " varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL"),
  "SELECT 1"
));
PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

-- Add employee_id_filesize if it doesn't exist
SET @columnname = 'employee_id_filesize';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " int DEFAULT NULL AFTER employee_id_filetype")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Check and rename verification_status to approval_status
SET @oldcolumn = 'verification_status';
SET @newcolumn = 'approval_status';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @oldcolumn)
  ) > 0,
  CONCAT("ALTER TABLE ", @tablename, " CHANGE COLUMN ", @oldcolumn, " ", @newcolumn, " enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending'"),
  "SELECT 1"
));
PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

-- Check and rename approval_date to approved_at
SET @oldcolumn = 'approval_date';
SET @newcolumn = 'approved_at';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @oldcolumn)
  ) > 0,
  CONCAT("ALTER TABLE ", @tablename, " CHANGE COLUMN ", @oldcolumn, " ", @newcolumn, " timestamp NULL DEFAULT NULL"),
  "SELECT 1"
));
PREPARE alterIfExists FROM @preparedStatement;
EXECUTE alterIfExists;
DEALLOCATE PREPARE alterIfExists;

-- Add approved_by if it doesn't exist
SET @columnname = 'approved_by';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " int DEFAULT NULL AFTER approved_at")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add phone if it doesn't exist
SET @columnname = 'phone';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER approved_by")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add foreign key for approved_by if it doesn't exist
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = @dbname
      AND TABLE_NAME = 'property_manager'
      AND CONSTRAINT_NAME = 'fk_property_manager_approved_by'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
  ) > 0,
  "SELECT 1",
  "ALTER TABLE property_manager ADD CONSTRAINT fk_property_manager_approved_by FOREIGN KEY (approved_by) REFERENCES users (id) ON DELETE SET NULL"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET FOREIGN_KEY_CHECKS = 1;

-- Display confirmation
SELECT 'Schema update completed successfully!' AS Status,
       'Properties table: approval_status and approved_at columns are ready.' AS Properties_Table,
       'Property_manager table: All columns renamed and added successfully.' AS Property_Manager_Table;
