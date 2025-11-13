#!/bin/bash

# ============================================================================
# DATABASE IMPORT AND MIGRATION SCRIPT
# This script imports the database and applies the corrected schema fixes
# ============================================================================

echo "Starting database import and migration..."

# Database credentials
DB_USER="root"
DB_PASS="root"
DB_NAME="rentigo_db"

# Get the directory where this script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

echo "Step 1: Dropping existing database (if exists)..."
mysql -u $DB_USER -p$DB_PASS -e "DROP DATABASE IF EXISTS $DB_NAME;"

echo "Step 2: Creating fresh database..."
mysql -u $DB_USER -p$DB_PASS -e "CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "Step 3: Importing base schema..."
mysql -u $DB_USER -p$DB_PASS $DB_NAME < "$SCRIPT_DIR/rentigo_final_db.sql"

if [ $? -ne 0 ]; then
    echo "ERROR: Failed to import base schema"
    exit 1
fi

echo "Step 4: Applying schema corrections..."
mysql -u $DB_USER -p$DB_PASS $DB_NAME < "$SCRIPT_DIR/fix_schema_corrected.sql"

if [ $? -ne 0 ]; then
    echo "ERROR: Failed to apply schema corrections"
    exit 1
fi

echo "============================================================================"
echo "SUCCESS: Database import and migration completed!"
echo "============================================================================"
echo ""
echo "Database: $DB_NAME"
echo "You can now test the application with all user roles:"
echo "  - Admin"
echo "  - Property Manager"
echo "  - Landlord"
echo "  - Tenant"
echo ""
