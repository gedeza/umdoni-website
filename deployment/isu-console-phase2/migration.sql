-- =====================================================================
-- ISU Console — Phase 2: User management audit log
-- Author: Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
-- Date: 2026-07-04
--
-- General ISU audit trail (admin created/deactivated/password reset, etc.).
-- Separate from `site_control` (which is specifically suspend/restore).
--
-- Run ONCE via cPanel > phpMyAdmin on DB `umdonigov_umdoni`.
-- =====================================================================

CREATE TABLE IF NOT EXISTS `isu_audit` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `action`     VARCHAR(60)  NOT NULL COMMENT 'e.g. admin.create, admin.deactivate, admin.reset_password',
    `detail`     VARCHAR(255) NULL     COMMENT 'human-readable summary (no secrets)',
    `actor_id`   INT UNSIGNED NULL     COMMENT 'isu_admins.id who performed it',
    `actor_name` VARCHAR(150) NULL,
    `ip_address` VARCHAR(45)  NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
