#!/bin/bash
# ============================================================
# SISARAYA Production Migration Fix Script
# Run this on your production server (srv1045082)
# ============================================================

set -e  # Exit on any error

echo "=========================================="
echo "SISARAYA Production Migration Fix"
echo "=========================================="
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Are you in the project root?"
    exit 1
fi

# Backup database first
echo "📦 Creating database backup..."
BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
mysqldump -u root -p sisaraya_workspace > "$BACKUP_FILE"
echo "✅ Backup saved to: $BACKUP_FILE"
echo ""

# Show current migration status
echo "📋 Current migration status:"
php artisan migrate:status | grep -E "(event_project|events)" || echo "No event-related migrations found"
echo ""

# Ask for confirmation
read -p "⚠️  This will fix the event_project table issue. Continue? (y/N) " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Aborted by user"
    exit 1
fi

# Apply SQL fix
echo "🔧 Applying SQL fix..."
mysql -u root -p sisaraya_workspace << 'EOF'
-- Drop orphaned table
DROP TABLE IF EXISTS `event_project`;

-- Recreate with proper foreign keys
CREATE TABLE IF NOT EXISTS `event_project` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `event_id` bigint unsigned NOT NULL,
    `project_id` bigint unsigned NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `event_project_event_id_project_id_unique` (`event_id`, `project_id`),
    KEY `event_project_event_id_foreign` (`event_id`),
    KEY `event_project_project_id_foreign` (`project_id`),
    CONSTRAINT `event_project_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `event_project_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mark migration as complete
INSERT INTO migrations (migration, batch) 
VALUES ('2025_10_13_050223_create_event_project_table', (SELECT IFNULL(MAX(batch), 0) + 1 FROM (SELECT MAX(batch) AS batch FROM migrations) AS temp))
ON DUPLICATE KEY UPDATE batch = batch;
EOF

echo "✅ SQL fix applied"
echo ""

# Run remaining migrations
echo "🚀 Running remaining migrations..."
php artisan migrate --force

echo ""
echo "=========================================="
echo "✅ Migration fix completed successfully!"
echo "=========================================="
echo ""
echo "Verification:"
php artisan migrate:status | tail -10

echo ""
echo "📝 Backup file: $BACKUP_FILE"
echo "Keep this file until you've verified everything works correctly."
