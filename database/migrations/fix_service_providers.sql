-- ============================================================================
-- FIX SERVICE_PROVIDERS TABLE
-- Run this to add missing 'company' and 'address' columns
-- ============================================================================

-- Ensure company column exists
SET @col_exists = (SELECT COUNT(*) FROM information_schema.columns
                   WHERE table_schema = 'rentigo_db'
                   AND table_name = 'service_providers'
                   AND column_name = 'company');

SET @sql = IF(@col_exists = 0,
  'ALTER TABLE `service_providers` ADD COLUMN `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL AFTER `name`',
  'SELECT "company already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ensure address column exists
SET @col_exists = (SELECT COUNT(*) FROM information_schema.columns
                   WHERE table_schema = 'rentigo_db'
                   AND table_name = 'service_providers'
                   AND column_name = 'address');

SET @sql = IF(@col_exists = 0,
  'ALTER TABLE `service_providers` ADD COLUMN `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci AFTER `email`',
  'SELECT "address already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SELECT 'Service providers table fixed! company and address columns added.' AS Result;
