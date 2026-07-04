<?php

namespace App\Models;

use PDO;

/**
 * IsuAdmin
 *
 * Standalone ISU (provider) admin accounts — a login system independent
 * of the municipal `users` table. All access is via prepared statements.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class IsuAdmin extends \Core\Repository
{
    /**
     * Fetch an active admin by email (for login).
     */
    public static function getActiveByEmail(string $email)
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare(
                'SELECT * FROM isu_admins WHERE email = :email AND active = 1 LIMIT 1'
            );
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Fetch an active admin by id (used to re-verify each request).
     */
    public static function getActiveById($id)
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare(
                'SELECT * FROM isu_admins WHERE id = :id AND active = 1 LIMIT 1'
            );
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Verify a plaintext password against a stored hash.
     */
    public static function passwordMatches(array $admin, string $password): bool
    {
        return isset($admin['password_hash'])
            && password_verify($password, $admin['password_hash']);
    }

    /**
     * Record a successful login (timestamp + IP).
     */
    public static function recordLogin($id, ?string $ip): void
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare(
                'UPDATE isu_admins SET last_login_at = NOW(), last_login_ip = :ip WHERE id = :id'
            );
            $stmt->bindValue(':ip', $ip, $ip === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (\Throwable $e) {
            // non-fatal
        }
    }

    /**
     * Set a new password hash and clear the must-change flag.
     */
    public static function setPassword($id, string $newHash): bool
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare(
                'UPDATE isu_admins SET password_hash = :h, must_change_password = 0 WHERE id = :id'
            );
            $stmt->bindValue(':h', $newHash, PDO::PARAM_STR);
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /* -------------------- management (Phase 2) -------------------- */

    /**
     * All admins (active and inactive), newest first.
     */
    public static function getAll(): array
    {
        try {
            $db = static::getDB();
            $stmt = $db->query('SELECT id, username, email, active, must_change_password,
                                       last_login_at, last_login_ip, created_at
                                FROM isu_admins ORDER BY created_at DESC, id DESC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function getById($id)
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare('SELECT * FROM isu_admins WHERE id = :id LIMIT 1');
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function emailExists(string $email): bool
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare('SELECT 1 FROM isu_admins WHERE email = :email LIMIT 1');
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            return (bool) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Create a new admin (forced to change password on first login).
     * Returns the new id, or null on failure.
     */
    public static function create(string $username, string $email, string $hash)
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare(
                'INSERT INTO isu_admins (username, email, password_hash, active, must_change_password)
                 VALUES (:u, :e, :h, 1, 1)'
            );
            $stmt->bindValue(':u', $username, PDO::PARAM_STR);
            $stmt->bindValue(':e', $email, PDO::PARAM_STR);
            $stmt->bindValue(':h', $hash, PDO::PARAM_STR);
            $stmt->execute();
            return (int) $db->lastInsertId();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function setActive($id, bool $active): bool
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare('UPDATE isu_admins SET active = :a WHERE id = :id');
            $stmt->bindValue(':a', $active ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Reset an admin's password to a new hash and force a change on next login.
     */
    public static function resetPassword($id, string $hash): bool
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare(
                'UPDATE isu_admins SET password_hash = :h, must_change_password = 1 WHERE id = :id'
            );
            $stmt->bindValue(':h', $hash, PDO::PARAM_STR);
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function countActive(): int
    {
        try {
            $db = static::getDB();
            return (int) $db->query('SELECT COUNT(*) FROM isu_admins WHERE active = 1')->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
