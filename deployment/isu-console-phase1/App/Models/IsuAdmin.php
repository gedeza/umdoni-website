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
}
