-- ============================================================
-- SISARAYA Production Database Migration Fix
-- Issue: event_project table created before events table
-- Date: 2025-10-22 (Updated)
-- ============================================================

-- Step 1: Drop orphaned/broken tables
DROP TABLE IF EXISTS `event_project`;
DROP TABLE IF EXISTS `event_user`;
DROP TABLE IF EXISTS `events`;

-- Step 2: Create events table
CREATE TABLE `events` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `description` text,
    `start_date` date DEFAULT NULL,
    `end_date` date DEFAULT NULL,
    `created_by` bigint unsigned DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `events_created_by_foreign` (`created_by`),
    CONSTRAINT `events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 3: Create event_user pivot table
CREATE TABLE `event_user` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `event_id` bigint unsigned NOT NULL,
    `user_id` bigint unsigned NOT NULL,
    `role` varchar(255) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `event_user_event_id_foreign` (`event_id`),
    KEY `event_user_user_id_foreign` (`user_id`),
    CONSTRAINT `event_user_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `event_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 4: Create event_project pivot table with proper foreign keys
CREATE TABLE `event_project` (
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

-- Step 5: Mark migrations as completed
-- Remove old problematic migration records
DELETE FROM migrations WHERE migration IN (
    '2025_10_13_050223_create_event_project_table',
    '2025_10_13_061314_drop_event_tables',
    '2025_10_21_000001_fix_event_project_orphaned_table',
    '2025_10_21_000002_recreate_event_project_table'
);

-- Mark the correct migration as completed
INSERT INTO migrations (migration, batch) 
VALUES ('2025_10_14_000003_create_events_table', (SELECT IFNULL(MAX(batch), 0) + 1 FROM (SELECT MAX(batch) AS batch FROM migrations) AS temp));

-- Note: After running this SQL, run: php artisan migrate --force
-- This will complete any remaining migrations
