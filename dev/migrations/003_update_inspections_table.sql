-- Migration: Update inspections table to use property_id and add missing fields
-- Date: 2025-01-16
-- Description: Fix database schema for inspection module

-- Step 1: Add new columns
ALTER TABLE `inspections`
ADD COLUMN `property_id` INT NULL AFTER `id`,
ADD COLUMN `issue_id` INT NULL AFTER `property_id`,
ADD COLUMN `scheduled_time` TIME NULL AFTER `scheduled_date`,
ADD COLUMN `notes` TEXT NULL COMMENT 'Scheduling notes' AFTER `scheduled_time`,
ADD COLUMN `inspection_notes` TEXT NULL COMMENT 'Findings after inspection' AFTER `notes`,
ADD COLUMN `manager_id` INT NULL COMMENT 'PM who scheduled the inspection' AFTER `inspection_notes`,
ADD COLUMN `landlord_id` INT NULL AFTER `manager_id`,
ADD COLUMN `tenant_id` INT NULL AFTER `landlord_id`;

-- Step 2: Migrate existing data (convert property address to property_id)
-- Note: This will try to match addresses, but manual verification may be needed
UPDATE inspections i
LEFT JOIN properties p ON i.property = p.address
SET i.property_id = p.id,
    i.landlord_id = p.landlord_id
WHERE p.id IS NOT NULL;

-- Step 3: Copy issues column to issue_id
UPDATE inspections
SET issue_id = CASE WHEN issues > 0 THEN issues ELSE NULL END;

-- Step 4: Drop old columns (only after verifying data migration)
-- IMPORTANT: Comment these out until you verify the migration worked correctly
-- ALTER TABLE `inspections` DROP COLUMN `property`;
-- ALTER TABLE `inspections` DROP COLUMN `issues`;

-- Step 5: Add foreign key constraints
ALTER TABLE `inspections`
ADD CONSTRAINT `fk_inspections_property`
    FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_inspections_issue`
    FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_inspections_manager`
    FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Step 6: Make property_id required after migration
-- ALTER TABLE `inspections` MODIFY `property_id` INT NOT NULL;
