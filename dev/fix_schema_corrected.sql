-- ============================================================================
-- CORRECTED SCHEMA MIGRATION SCRIPT
-- This script fixes the column mismatches between code and database
-- Safe to run multiple times (idempotent)
-- ============================================================================

USE `rentigo_db`;

-- ============================================================================
-- STEP 1: FIX PROPERTY_MANAGER TABLE
-- ============================================================================

-- Set variables for conditional column operations
SET @tablename = 'property_manager';
SET @dbname = DATABASE();

-- -------------------------------------------
-- 1.1: First, ADD 'approved' to the verification_status enum (if not already there)
-- -------------------------------------------
SET @columnname = 'verification_status';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) LIKE '%approved%',
  "SELECT 'approved already in enum' AS message",
  "ALTER TABLE property_manager MODIFY COLUMN verification_status ENUM('pending','verified','rejected','approved') DEFAULT 'pending'"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- -------------------------------------------
-- 1.2: Now UPDATE 'verified' to 'approved' (now that 'approved' is valid)
-- -------------------------------------------
UPDATE `property_manager`
SET `verification_status` = 'approved'
WHERE `verification_status` = 'verified';

-- -------------------------------------------
-- 1.3: Check if we need to rename verification_status to approval_status
-- -------------------------------------------
SET @columnExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @dbname
  AND TABLE_NAME = @tablename
  AND COLUMN_NAME = 'approval_status');

SET @oldColumnExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @dbname
  AND TABLE_NAME = @tablename
  AND COLUMN_NAME = 'verification_status');

-- Only rename if approval_status doesn't exist but verification_status does
SET @preparedStatement = (SELECT IF(
  @columnExists = 0 AND @oldColumnExists > 0,
  "ALTER TABLE property_manager CHANGE COLUMN verification_status approval_status ENUM('pending','approved','rejected') DEFAULT 'pending'",
  "SELECT 'approval_status already exists or verification_status does not exist' AS message"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- -------------------------------------------
-- 1.4: Rename document_filename to employee_id_filename (if needed)
-- -------------------------------------------
SET @columnExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @dbname
  AND TABLE_NAME = @tablename
  AND COLUMN_NAME = 'employee_id_filename');

SET @oldColumnExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @dbname
  AND TABLE_NAME = @tablename
  AND COLUMN_NAME = 'document_filename');

SET @preparedStatement = (SELECT IF(
  @columnExists = 0 AND @oldColumnExists > 0,
  "ALTER TABLE property_manager CHANGE COLUMN document_filename employee_id_filename VARCHAR(255) DEFAULT NULL",
  "SELECT 'employee_id_filename already exists or document_filename does not exist' AS message"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- -------------------------------------------
-- 1.5: Rename document_mimetype to employee_id_filetype (if needed)
-- -------------------------------------------
SET @columnExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @dbname
  AND TABLE_NAME = @tablename
  AND COLUMN_NAME = 'employee_id_filetype');

SET @oldColumnExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @dbname
  AND TABLE_NAME = @tablename
  AND COLUMN_NAME = 'document_mimetype');

SET @preparedStatement = (SELECT IF(
  @columnExists = 0 AND @oldColumnExists > 0,
  "ALTER TABLE property_manager CHANGE COLUMN document_mimetype employee_id_filetype VARCHAR(100) DEFAULT NULL",
  "SELECT 'employee_id_filetype already exists or document_mimetype does not exist' AS message"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- -------------------------------------------
-- 1.6: Add employee_id_filesize column (if not exists)
-- -------------------------------------------
SET @columnname = 'employee_id_filesize';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'employee_id_filesize already exists' AS message",
  "ALTER TABLE property_manager ADD COLUMN employee_id_filesize INT DEFAULT NULL AFTER employee_id_filetype"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- -------------------------------------------
-- 1.7: Rename approval_date to approved_at (if needed)
-- -------------------------------------------
SET @columnExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @dbname
  AND TABLE_NAME = @tablename
  AND COLUMN_NAME = 'approved_at');

SET @oldColumnExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @dbname
  AND TABLE_NAME = @tablename
  AND COLUMN_NAME = 'approval_date');

SET @preparedStatement = (SELECT IF(
  @columnExists = 0 AND @oldColumnExists > 0,
  "ALTER TABLE property_manager CHANGE COLUMN approval_date approved_at TIMESTAMP NULL DEFAULT NULL",
  "SELECT 'approved_at already exists or approval_date does not exist' AS message"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- -------------------------------------------
-- 1.8: Add approved_by column (if not exists)
-- -------------------------------------------
SET @columnname = 'approved_by';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'approved_by already exists' AS message",
  "ALTER TABLE property_manager ADD COLUMN approved_by INT DEFAULT NULL AFTER approved_at"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- -------------------------------------------
-- 1.9: Add phone column (if not exists)
-- -------------------------------------------
SET @columnname = 'phone';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'phone already exists' AS message",
  "ALTER TABLE property_manager ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER approved_by"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ============================================================================
-- STEP 2: FIX PROPERTIES TABLE
-- ============================================================================

SET @tablename = 'properties';

-- -------------------------------------------
-- 2.1: Add approval_status column (if not exists)
-- -------------------------------------------
SET @columnname = 'approval_status';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'approval_status already exists' AS message",
  "ALTER TABLE properties ADD COLUMN approval_status ENUM('pending','approved','rejected') DEFAULT 'pending' AFTER status"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- -------------------------------------------
-- 2.2: Add approved_at column (if not exists)
-- -------------------------------------------
SET @columnname = 'approved_at';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'approved_at already exists' AS message",
  "ALTER TABLE properties ADD COLUMN approved_at TIMESTAMP NULL DEFAULT NULL AFTER approval_status"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

SELECT '=== PROPERTY_MANAGER TABLE STRUCTURE ===' AS Info;
SHOW COLUMNS FROM property_manager;

SELECT '=== PROPERTIES TABLE STRUCTURE ===' AS Info;
SHOW COLUMNS FROM properties;

SELECT 'Migration completed successfully!' AS Status;
