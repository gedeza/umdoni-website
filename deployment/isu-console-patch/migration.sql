-- =====================================================================
-- ISU Console — Database Migration
-- Author: Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
-- Purpose: Add provider (ISU) admin flag + service-control audit table
--
-- Run ONCE against the production database (umdonigov_umdoni) via
-- cPanel > phpMyAdmin, or:
--   mysql -u umdonigov_admin -p umdonigov_umdoni < migration.sql
-- =====================================================================

-- 1) Mark which accounts belong to ISU Technologies (the service provider).
--    A normal municipal admin — even a super-admin — has is_isu = 0 and
--    therefore CANNOT see or use the ISU console.
ALTER TABLE `users`
    ADD COLUMN `is_isu` TINYINT(1) NOT NULL DEFAULT 0
    COMMENT 'ISU Technologies provider admin (1 = yes). Grants access to /isu console.';

-- 2) Grant ISU access to the project owner.
UPDATE `users` SET `is_isu` = 1 WHERE `email` = 'nhlanhla@isutech.co.za';

-- 3) Audit trail for every suspend / restore action (who, when, why).
--    The on/off switch itself is a flag file (storage/site-suspended.flag)
--    so the site can be gated even if MySQL is unavailable; this table is
--    the permanent record of provider actions.
CREATE TABLE IF NOT EXISTS `site_control` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `action`     ENUM('suspend','restore') NOT NULL,
    `reason`     VARCHAR(255) NULL COMMENT 'Internal note, e.g. invoice #1234 overdue',
    `user_id`    INT NULL COMMENT 'ISU admin who performed the action',
    `actor_name` VARCHAR(150) NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
