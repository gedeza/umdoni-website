-- =====================================================================
-- ISU Console — Phase 4: Patch deploy tool
-- Author: Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
-- Date: 2026-07-04
--
-- Records each applied patch ZIP so it can be rolled back. The actual
-- backed-up files + manifest live on disk under
-- storage/isu-patches/backups/<token>/.
--
-- Run ONCE via cPanel > phpMyAdmin, OR (once Phase 3 is live) drop this file
-- into /migrations and run it from the console's Database page.
-- =====================================================================

CREATE TABLE IF NOT EXISTS `isu_patches` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `token`          VARCHAR(64)  NOT NULL,
    `original_name`  VARCHAR(191) NULL,
    `file_count`     INT          NOT NULL DEFAULT 0,
    `backup_dir`     VARCHAR(255) NULL COMMENT 'relative path under project root',
    `status`         ENUM('applied','rolled_back') NOT NULL DEFAULT 'applied',
    `applied_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `applied_by`     INT          NULL,
    `actor_name`     VARCHAR(150) NULL,
    `rolled_back_at` DATETIME     NULL,
    PRIMARY KEY (`id`),
    KEY `idx_applied_at` (`applied_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
