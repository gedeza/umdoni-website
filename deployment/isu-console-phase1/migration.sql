-- =====================================================================
-- ISU Console — Phase 1: Independent authentication
-- Author: Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
-- Date: 2026-07-04
--
-- Creates a standalone ISU admin login table (separate from municipal
-- `users`) so the provider console has its own credentials and session,
-- usable even while the municipal side is suspended.
--
-- Run ONCE via cPanel > phpMyAdmin on DB `umdonigov_umdoni`.
-- =====================================================================

CREATE TABLE IF NOT EXISTS `isu_admins` (
    `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`             VARCHAR(100) NOT NULL,
    `email`                VARCHAR(150) NOT NULL,
    `password_hash`        VARCHAR(255) NOT NULL,
    `active`               TINYINT(1)   NOT NULL DEFAULT 1,
    `must_change_password` TINYINT(1)   NOT NULL DEFAULT 1,
    `last_login_at`        DATETIME     NULL,
    `last_login_ip`        VARCHAR(45)  NULL,
    `created_at`           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Seed the first ISU admin. Temp password is set and must be changed on
-- first login (must_change_password = 1). The plaintext temp password is
-- delivered separately (not stored here — only the bcrypt hash is).
INSERT INTO `isu_admins` (`username`, `email`, `password_hash`, `active`, `must_change_password`)
SELECT 'Nhlanhla Mnyandu', 'nhlanhla@isutech.co.za',
       '$2y$12$7svSf2mURe6U6iI3mJvvle/o.ugoLJjNm0IHsUir6bzVhKcdmBEZi', 1, 1
WHERE NOT EXISTS (
    SELECT 1 FROM `isu_admins` WHERE `email` = 'nhlanhla@isutech.co.za'
);
