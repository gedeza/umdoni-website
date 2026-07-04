-- =====================================================================
-- ISU Console — Phase 3: DB tools (migrations tracking)
-- Author: Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
-- Date: 2026-07-04
--
-- Records which migration files (shipped in /migrations) have been run,
-- so the console's migration runner executes each one exactly once.
--
-- This is the ONE migration you still run by hand in phpMyAdmin (it
-- bootstraps the runner). After this, future migrations run from the UI.
--
-- Run ONCE via cPanel > phpMyAdmin on DB `umdonigov_umdoni`.
-- =====================================================================

CREATE TABLE IF NOT EXISTS `isu_migrations` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `filename`   VARCHAR(191) NOT NULL,
    `ran_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ran_by`     INT          NULL COMMENT 'isu_admins.id',
    `actor_name` VARCHAR(150) NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
