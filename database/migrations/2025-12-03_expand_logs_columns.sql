-- Migration: Expand logs table columns for error logging
-- Date: 2025-12-03
-- Author: Nhlanhla Mnyandu
-- Description: Expand actions and location columns to accommodate detailed error messages and user agent strings

-- Expand actions column from varchar(45) to varchar(500)
-- This column now stores error messages and action descriptions
ALTER TABLE `logs` MODIFY `actions` VARCHAR(500);

-- Expand location column from varchar(45) to varchar(500)
-- This column now stores IP address + User Agent string (format: "IP | User Agent")
ALTER TABLE `logs` MODIFY `location` VARCHAR(500);

-- Note: These changes were applied directly to production database via phpMyAdmin on 2025-12-03
-- No rollback needed as these are non-destructive schema expansions
