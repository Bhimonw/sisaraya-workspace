-- ============================================================
-- SISARAYA Production Database Migration Fix
-- Issue: event_project table created before events table
-- Date: 2025-10-21
-- ============================================================

-- Step 1: Drop the orphaned event_project table (no foreign keys work)
DROP TABLE IF EXISTS `event_project`;

-- Step 2: Verify events table exists (should exist from 2025_10_14_000003_create_events_table.php)
-- If not, create it first:
-- CREATE TABLE IF NOT EXISTS `events` (
--     `id` bigint unsigned NOT NULL AUTO_INCREMENT,
--     `title` varchar(255) NOT NULL,
--     `description` text,
--     `start_date` date DEFAULT NULL,
--     `end_date` date DEFAULT NULL,
--     `created_by` bigint unsigned DEFAULT NULL,
--     `created_at` timestamp NULL DEFAULT NULL,
--     `updated_at` timestamp NULL DEFAULT NULL,
--     PRIMARY KEY (`id`),
--     KEY `events_created_by_foreign` (`created_by`),
--     CONSTRAINT `events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 3: Create event_project table with proper foreign keys
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

-- Step 4: Mark the migration as completed
-- INSERT INTO migrations (migration, batch) 
-- VALUES ('2025_10_13_050223_create_event_project_table', (SELECT MAX(batch) FROM migrations));

-- Note: After running this SQL, run: php artisan migrate
-- This will complete any remaining migrations
