-- ============================================================================
-- STEP 1: CHECK CURRENT DATA
-- Run this first to see what data exists
-- ============================================================================

-- Check what values exist in properties.status
SELECT 'Properties status values:' AS Info;
SELECT DISTINCT status, COUNT(*) as count FROM properties GROUP BY status;

-- Check what values exist in property_manager.verification_status
SELECT 'Property_manager verification_status values:' AS Info;
SELECT DISTINCT verification_status, COUNT(*) as count FROM property_manager GROUP BY verification_status;

-- Check all columns in property_manager table
SELECT 'Property_manager table structure:' AS Info;
SHOW COLUMNS FROM property_manager;

-- Check all columns in properties table
SELECT 'Properties table structure:' AS Info;
SHOW COLUMNS FROM properties;
