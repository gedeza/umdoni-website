-- =====================================================================
-- ISU Console — Phase 5: Support / Ticketing
-- Author: Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
-- Date: 2026-07-05
--
-- Run from the console: Database page -> Migrations -> Run.
-- =====================================================================

CREATE TABLE IF NOT EXISTS `isu_tickets` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ref`               VARCHAR(20)  NULL,
    `subject`           VARCHAR(200) NOT NULL,
    `description`       TEXT         NULL,
    `category`          VARCHAR(50)  NULL,
    `priority`          ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
    `status`            ENUM('open','in_progress','on_hold','resolved','closed') NOT NULL DEFAULT 'open',
    `requester_name`    VARCHAR(150) NULL,
    `requester_contact` VARCHAR(150) NULL,
    `assigned_to`       VARCHAR(150) NULL,
    `created_by`        INT          NULL COMMENT 'isu_admins.id',
    `created_at`        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME     NULL,
    `resolved_at`       DATETIME     NULL,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `isu_ticket_replies` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ticket_id`   INT UNSIGNED NOT NULL,
    `author_id`   INT          NULL,
    `author_name` VARCHAR(150) NULL,
    `body`        TEXT         NOT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ticket` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
