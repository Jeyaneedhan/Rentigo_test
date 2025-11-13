# Database Schema Fix - Instructions

## Problem Summary

The application code expects database columns that don't exist in the current schema:

### Property_manager table issues:
- Code expects: `approval_status` | DB has: `verification_status`
- Code expects: `employee_id_filename` | DB has: `document_filename`
- Code expects: `employee_id_filetype` | DB has: `document_mimetype`
- Code expects: `employee_id_filesize` | DB has: *missing*
- Code expects: `approved_at` | DB has: `approval_date`
- Code expects: `approved_by` | DB has: *missing*
- Code expects: `phone` | DB has: *missing*

### Properties table issues:
- Code expects: `approval_status` | DB has: *missing*
- Code expects: `approved_at` | DB has: *missing*

## Solution

The corrected migration script (`fix_schema_corrected.sql`) will:
1. Add missing columns
2. Rename existing columns to match code expectations
3. Handle enum value conversions properly (verified → approved)

## How to Apply the Fix

### Method 1: Using phpMyAdmin (Easiest)

1. Open phpMyAdmin
2. Select the `rentigo_db` database
3. Click on the "SQL" tab
4. Open the file `dev/fix_schema_corrected.sql`
5. Copy ALL the contents
6. Paste into the SQL query box
7. Click "Go" to execute

### Method 2: Using Command Line

```bash
cd /home/user/Rentigo_test/dev
mysql -u root -proot rentigo_db < fix_schema_corrected.sql
```

### Method 3: Fresh Import (Recommended if you have no important data)

This will drop the database and reimport everything with fixes:

```bash
cd /home/user/Rentigo_test/dev
./import_and_fix_db_corrected.sh
```

## What Gets Fixed

### Property_manager table:
- ✅ Adds 'approved' to verification_status enum
- ✅ Updates 'verified' values to 'approved'
- ✅ Renames `verification_status` → `approval_status`
- ✅ Renames `document_filename` → `employee_id_filename`
- ✅ Renames `document_mimetype` → `employee_id_filetype`
- ✅ Adds `employee_id_filesize` column
- ✅ Renames `approval_date` → `approved_at`
- ✅ Adds `approved_by` column
- ✅ Adds `phone` column

### Properties table:
- ✅ Adds `approval_status` column
- ✅ Adds `approved_at` column

## Verification

After running the migration, you can verify the changes:

```sql
-- Check property_manager columns
SHOW COLUMNS FROM property_manager;

-- Check properties columns
SHOW COLUMNS FROM properties;

-- Check approval_status values
SELECT DISTINCT approval_status, COUNT(*)
FROM property_manager
GROUP BY approval_status;
```

## Important Notes

- ⚠️ The script is **idempotent** - safe to run multiple times
- ⚠️ It checks if columns exist before adding/renaming them
- ⚠️ No data will be lost during migration
- ⚠️ Enum values are properly converted (verified → approved)

## Testing After Migration

Test all user roles to ensure no errors:

1. **Admin Login** - Dashboard should load without errors
2. **Property Manager Login** - Should see assigned properties
3. **Landlord Login** - Should see property listings and bookings
4. **Tenant Login** - Should see bookings and lease agreements

## Previous Errors That Will Be Fixed

All these errors will be resolved after migration:

- ❌ `Unknown column 'approval_status'` → ✅ Fixed
- ❌ `Unknown column 'employee_id_filename'` → ✅ Fixed
- ❌ `Data truncated for column 'verification_status'` → ✅ Fixed
- ❌ `Call to undefined method getAllProperties()` → ✅ Fixed (code updated)
- ❌ `Method view() not compatible` → ✅ Fixed (code updated)

## Files Changed

### Database Migration:
- `dev/fix_schema_corrected.sql` - Corrected migration script
- `dev/import_and_fix_db_corrected.sh` - Automated import script

### Code Fixes (Already Applied):
- Controllers: Admin.php, Manager.php, Bookings.php, LeaseAgreements.php, Maintenance.php, Messages.php, Payments.php
- Models: M_Properties.php, M_Maintenance.php, M_Users.php
- Views: Multiple view files updated with new URL patterns

## Support

If you encounter any issues:
1. Check the verification queries output
2. Ensure you're using MySQL/MariaDB (not SQLite)
3. Verify database user has ALTER TABLE privileges
4. Check error messages for specific column or table names
