<?php

namespace App\Models;

use PDO;

/**
 * IsuAudit
 *
 * General provider-console audit trail (admin management, and later DB /
 * deploy actions). Best-effort logging — never breaks the calling action.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class IsuAudit extends \Core\Repository
{
    /**
     * Record an audited action.
     *
     * @param array $actor  ['id' => .., 'username' => .., 'email' => ..]
     */
    public static function log(string $action, ?string $detail, array $actor): void
    {
        try {
            $db = static::getDB();
            $sql = 'INSERT INTO isu_audit (action, detail, actor_id, actor_name, ip_address, created_at)
                    VALUES (:action, :detail, :actor_id, :actor_name, :ip, NOW())';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':action', $action, PDO::PARAM_STR);
            $stmt->bindValue(':detail', $detail, $detail === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':actor_id', $actor['id'] ?? null, ($actor['id'] ?? null) === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $name = $actor['username'] ?? ($actor['email'] ?? null);
            $stmt->bindValue(':actor_name', $name, $name === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR'] ?? null, PDO::PARAM_STR);
            $stmt->execute();
        } catch (\Throwable $e) {
            // best-effort
        }
    }

    /**
     * Recent audit rows (most recent first).
     */
    public static function recent(int $limit = 25): array
    {
        try {
            $db = static::getDB();
            $limit = max(1, min(100, $limit));
            $stmt = $db->prepare('SELECT * FROM isu_audit ORDER BY created_at DESC, id DESC LIMIT ' . $limit);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
