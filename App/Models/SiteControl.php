<?php

namespace App\Models;

use PDO;

/**
 * SiteControl
 *
 * Provider (ISU) service kill-switch and audit trail.
 *
 * The authoritative on/off state is a FLAG FILE
 * (storage/site-suspended.flag) rather than a DB row, so the site can be
 * gated on every request cheaply and even if MySQL is unreachable. The
 * `site_control` table is the permanent audit record of who suspended /
 * restored service and why.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class SiteControl extends \Core\Repository
{
    /**
     * Absolute path to the suspension flag file.
     * dirname(__DIR__, 2) => project root (this file lives in App/Models/).
     */
    public static function flagFile(): string
    {
        return dirname(__DIR__, 2) . '/storage/site-suspended.flag';
    }

    /**
     * Is the site currently suspended? Cheap, no DB required.
     */
    public static function isSuspended(): bool
    {
        return is_file(self::flagFile());
    }

    /**
     * Metadata recorded inside the flag file (json) at suspend time.
     * Returns [] if not suspended or unreadable.
     */
    public static function suspensionInfo(): array
    {
        if (!self::isSuspended()) {
            return [];
        }
        $raw = @file_get_contents(self::flagFile());
        $data = json_decode((string) $raw, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Suspend the site: create the flag file and log an audit row.
     */
    public static function suspend(array $actor, ?string $reason = null): bool
    {
        $dir = dirname(self::flagFile());
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $payload = json_encode([
            'suspended_at' => date('c'),
            'reason'       => $reason,
            'user_id'      => $actor['user_id'] ?? null,
            'actor_name'   => self::actorName($actor),
        ]);

        $ok = @file_put_contents(self::flagFile(), $payload) !== false;
        if ($ok) {
            self::log('suspend', $reason, $actor);
        }
        return $ok;
    }

    /**
     * Restore the site: remove the flag file and log an audit row.
     */
    public static function restore(array $actor, ?string $reason = null): bool
    {
        $ok = true;
        if (self::isSuspended()) {
            $ok = @unlink(self::flagFile());
        }
        if ($ok) {
            self::log('restore', $reason, $actor);
        }
        return $ok;
    }

    /**
     * Is the given user an ISU provider admin? Authoritative DB check
     * (not trusting the session alone).
     */
    public static function isIsuAdmin($userId): bool
    {
        if (empty($userId)) {
            return false;
        }
        try {
            $db = static::getDB();
            $stmt = $db->prepare('SELECT is_isu FROM users WHERE user_id = :id LIMIT 1');
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $stmt->fetchColumn() === 1;
        } catch (\Throwable $e) {
            // Fail closed: if we cannot verify, deny provider access.
            return false;
        }
    }

    /**
     * Recent audit history (most recent first).
     */
    public static function history(int $limit = 25): array
    {
        try {
            $db = static::getDB();
            $limit = max(1, min(100, $limit));
            $stmt = $db->prepare(
                'SELECT * FROM site_control ORDER BY created_at DESC, id DESC LIMIT ' . $limit
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Insert an audit row. Failure here must never break the switch.
     */
    private static function log(string $action, ?string $reason, array $actor): void
    {
        try {
            $db = static::getDB();
            $sql = 'INSERT INTO site_control (action, reason, user_id, actor_name, ip_address, created_at)
                    VALUES (:action, :reason, :user_id, :actor_name, :ip, NOW())';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':action', $action, PDO::PARAM_STR);
            $stmt->bindValue(':reason', $reason, $reason === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $actor['user_id'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':actor_name', self::actorName($actor), PDO::PARAM_STR);
            $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR'] ?? null, PDO::PARAM_STR);
            $stmt->execute();
        } catch (\Throwable $e) {
            // Audit logging is best-effort; the flag file is the source of truth.
        }
    }

    private static function actorName(array $actor): ?string
    {
        $name = trim(($actor['username'] ?? '') . ' ' . ($actor['surname'] ?? ''));
        if ($name !== '') {
            return $name;
        }
        return $actor['email'] ?? null;
    }
}
