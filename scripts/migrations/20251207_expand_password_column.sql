-- Migration: Expand users.password column to support bcrypt hashes
-- Date: 2025-12-07
-- Author: Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
-- Task: Task #4 - Admin User Creation & Security Hardening
--
-- PROBLEM:
-- The users.password column is currently VARCHAR(45) which truncates bcrypt password hashes.
-- Bcrypt hashes are 60 characters long, so they get cut off at 45 characters, making
-- authentication impossible after password hashing is implemented.
--
-- SOLUTION:
-- Expand the column to VARCHAR(255) to support:
-- - Bcrypt hashes (60 chars)
-- - Future password hashing algorithms (may be longer)
-- - Legacy plaintext passwords (during migration period)
--
-- IMPACT:
-- - Existing plaintext passwords will continue to work (no data loss)
-- - New hashed passwords will be stored correctly
-- - Legacy passwords will be upgraded to hashed on next login
-- - No user data migration required
--
-- TESTING:
-- Before deployment, verify on local database:
-- 1. Run this migration
-- 2. Create test user with password
-- 3. Verify password hash is 60 characters
-- 4. Test login with new user
-- 5. Test login with existing user (legacy password)
--
-- DEPLOYMENT:
-- 1. Backup production database first!
-- 2. Run this SQL via cPanel phpMyAdmin or MySQL command line
-- 3. Verify column change: SHOW COLUMNS FROM users LIKE 'password';
-- 4. Deploy code changes (Profile.php, UserModel.php)
-- 5. Test user login immediately after deployment

-- =============================================================================
-- MIGRATION (Forward)
-- =============================================================================

USE umdonigov_umdoni;

-- Expand password column from VARCHAR(45) to VARCHAR(255)
ALTER TABLE `users`
MODIFY COLUMN `password` VARCHAR(255) DEFAULT NULL
COMMENT 'Supports bcrypt (60 chars) and future hashing algorithms';

-- Verify the change
SELECT
    COLUMN_NAME,
    COLUMN_TYPE,
    CHARACTER_MAXIMUM_LENGTH,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'umdonigov_umdoni'
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'password';

-- Expected result:
-- COLUMN_NAME: password
-- COLUMN_TYPE: varchar(255)
-- CHARACTER_MAXIMUM_LENGTH: 255
-- COLUMN_COMMENT: Supports bcrypt (60 chars) and future hashing algorithms

-- =============================================================================
-- ROLLBACK (In case of issues)
-- =============================================================================
-- WARNING: Only run rollback if you need to revert and NO hashed passwords exist yet!
-- Running this after hashed passwords exist will truncate them and break logins!
--
-- ALTER TABLE `users`
-- MODIFY COLUMN `password` VARCHAR(45) DEFAULT NULL;

-- =============================================================================
-- POST-MIGRATION VERIFICATION
-- =============================================================================

-- Count users by password type (run after some users login and passwords upgrade)
-- Legacy plaintext passwords: Usually 32 chars (MD5) or similar
-- Bcrypt hashes: Always 60 chars starting with $2y$

SELECT
    CASE
        WHEN password IS NULL THEN 'NULL (OAuth/Cognito users)'
        WHEN LENGTH(password) = 60 AND password LIKE '$2y$%' THEN 'Bcrypt hashed (secure)'
        WHEN LENGTH(password) = 60 AND password LIKE '$2a$%' THEN 'Bcrypt hashed (secure)'
        WHEN LENGTH(password) < 60 THEN 'Legacy plaintext/MD5 (needs upgrade)'
        ELSE 'Unknown format'
    END AS password_type,
    COUNT(*) as count,
    CONCAT(ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM users), 1), '%') as percentage
FROM users
GROUP BY password_type
ORDER BY count DESC;

-- Sample expected output after migration and some logins:
-- password_type                        | count | percentage
-- -------------------------------------|-------|------------
-- Bcrypt hashed (secure)              |   5   | 50.0%
-- Legacy plaintext/MD5 (needs upgrade)|   4   | 40.0%
-- NULL (OAuth/Cognito users)          |   1   | 10.0%

-- =============================================================================
-- NOTES
-- =============================================================================
-- 1. This migration is safe to run - it only expands the column size
-- 2. Existing data is preserved - no password truncation will occur
-- 3. The migration takes effect immediately (ALTER TABLE is atomic)
-- 4. Legacy passwords will automatically upgrade when users next login
-- 5. Monitor the password_type query above over time - legacy should decrease
-- 6. Once all passwords are hashed, you can remove legacy support from code
--
-- BACKUP REMINDER:
-- Before running ANY database migration, always create a backup:
-- mysqldump -h reseller142.aserv.co.za -u umdonigov_admin -p umdonigov_umdoni > backup_before_password_migration.sql
