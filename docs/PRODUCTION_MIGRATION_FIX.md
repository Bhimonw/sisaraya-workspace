# Production Migration Fix Guide

## Issue Summary
The migration `2025_10_13_050223_create_event_project_table.php` attempted to create a pivot table with foreign key constraints to the `events` table, but the `events` table hadn't been created yet (it's created by `2025_10_14_000003_create_events_table.php`).

This left the database in an inconsistent state:
- ✅ `event_project` table created (structure only)
- ❌ Foreign key constraints failed
- ❌ Migration marked as incomplete

## Error Messages
```
SQLSTATE[HY000]: General error: 1824 Failed to open the referenced table 'events'
SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'event_project' already exists
```

## Solution for Production Server

### Option 1: SQL Script (Recommended for MySQL Production)

**Step 1:** Backup database first
```bash
mysqldump -u root -p sisaraya_workspace > backup_$(date +%Y%m%d_%H%M%S).sql
```

**Step 2:** Run the fix SQL script
```bash
mysql -u root -p sisaraya_workspace < database/migrations/FIX_PRODUCTION_EVENT_PROJECT.sql
```

This script will:
- Drop broken event tables
- Recreate `events` table
- Create `event_user` pivot table
- Create `event_project` pivot table with proper foreign keys
- Clean up migration records
- Mark migration as completed

**Step 3:** Continue with remaining migrations
```bash
php artisan migrate --force
```

### Option 2: Fresh Migration (Use only if database is empty/test environment)

```bash
# WARNING: This drops ALL data
php artisan migrate:fresh --seed
```

### Option 3: Laravel Migration Fix (Alternative)

If you prefer to use Laravel migrations instead of SQL:

**Step 1:** Pull the latest code with migration fixes
```bash
git pull origin main
```

**Step 2:** The new migrations will handle the fix:
- `2025_10_21_000001_fix_event_project_orphaned_table.php` - Drops orphaned table
- `2025_10_21_000002_recreate_event_project_table.php` - Recreates with proper order

**Step 3:** Run migrations
```bash
php artisan migrate
```

## Verification

After applying the fix, verify:

```bash
# Check table exists with foreign keys
mysql -u your_username -p your_database_name -e "SHOW CREATE TABLE event_project\G"

# Check migrations table
mysql -u your_username -p your_database_name -e "SELECT * FROM migrations WHERE migration LIKE '%event%' ORDER BY id;"
```

Expected output should show:
- `event_project` table with foreign key constraints
- Migration marked as completed in `migrations` table

## Root Cause

The migration timestamps were ordered incorrectly:
- `2025_10_13_050223_create_event_project_table.php` (runs first) ❌
- `2025_10_13_061314_drop_event_tables.php` (drops events if exists)
- `2025_10_14_000003_create_events_table.php` (creates events) ✅

The fix ensures `event_project` is created AFTER `events` table exists.

## Prevention

For future migrations:
1. Always create parent tables before pivot/child tables
2. Use proper timestamp ordering in migration filenames
3. Test migrations on fresh database before deploying
4. Consider using `php artisan migrate:status` to check migration state

## Rollback (if needed)

If you need to rollback these changes:

```sql
DROP TABLE IF EXISTS `event_project`;
DELETE FROM migrations WHERE migration = '2025_10_13_050223_create_event_project_table';
DELETE FROM migrations WHERE migration = '2025_10_21_000001_fix_event_project_orphaned_table';
DELETE FROM migrations WHERE migration = '2025_10_21_000002_recreate_event_project_table';
```

Then re-run migrations in correct order.
