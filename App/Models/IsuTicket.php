<?php

namespace App\Models;

use PDO;

/**
 * IsuTicket
 *
 * Support tickets + threaded replies for the ISU console (ISU-only: staff
 * log requests uMdoni raise by email/phone and track them to resolution).
 * All access via prepared statements.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class IsuTicket extends \Core\Repository
{
    public static function statuses(): array
    {
        return [
            'open'        => 'Open',
            'in_progress' => 'In Progress',
            'on_hold'     => 'On Hold',
            'resolved'    => 'Resolved',
            'closed'      => 'Closed',
        ];
    }

    public static function priorities(): array
    {
        return ['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'];
    }

    public static function categories(): array
    {
        return ['Website issue', 'Content update', 'Hosting/Email', 'Security', 'Feature request', 'Other'];
    }

    /** Statuses considered "open" (still needing attention). */
    private static $openStatuses = ['open', 'in_progress', 'on_hold'];

    /**
     * Create a ticket. Returns the new id (with a UMD-#### ref), or null.
     */
    public static function create(array $d)
    {
        try {
            $db = static::getDB();
            $sql = 'INSERT INTO isu_tickets
                    (subject, description, category, priority, status, requester_name, requester_contact, created_by, created_at)
                    VALUES (:subject, :description, :category, :priority, "open", :rname, :rcontact, :by, NOW())';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':subject', $d['subject'], PDO::PARAM_STR);
            $stmt->bindValue(':description', $d['description'] ?? null);
            $stmt->bindValue(':category', $d['category'] ?? null);
            $stmt->bindValue(':priority', in_array($d['priority'] ?? '', array_keys(self::priorities()), true) ? $d['priority'] : 'normal');
            $stmt->bindValue(':rname', $d['requester_name'] ?? null);
            $stmt->bindValue(':rcontact', $d['requester_contact'] ?? null);
            $stmt->bindValue(':by', $d['created_by'] ?? null, ($d['created_by'] ?? null) === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->execute();
            $id = (int) $db->lastInsertId();

            $ref = 'UMD-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
            $db->prepare('UPDATE isu_tickets SET ref = :r WHERE id = :id')
               ->execute([':r' => $ref, ':id' => $id]);
            return $id;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * List tickets, optionally filtered by status. Open first, then newest.
     */
    public static function all(?string $status = null): array
    {
        try {
            $db = static::getDB();
            if ($status !== null && $status !== '' && array_key_exists($status, self::statuses())) {
                $stmt = $db->prepare('SELECT * FROM isu_tickets WHERE status = :s ORDER BY created_at DESC, id DESC');
                $stmt->bindValue(':s', $status, PDO::PARAM_STR);
                $stmt->execute();
            } else {
                $stmt = $db->query("SELECT * FROM isu_tickets
                                    ORDER BY FIELD(status,'open','in_progress','on_hold','resolved','closed'),
                                             created_at DESC, id DESC");
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function getById($id)
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare('SELECT * FROM isu_tickets WHERE id = :id LIMIT 1');
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Counts keyed by status, plus 'all'.
     */
    public static function counts(): array
    {
        $out = ['all' => 0];
        foreach (array_keys(self::statuses()) as $s) { $out[$s] = 0; }
        try {
            $db = static::getDB();
            $rows = $db->query('SELECT status, COUNT(*) c FROM isu_tickets GROUP BY status')
                       ->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $out[$r['status']] = (int) $r['c'];
                $out['all'] += (int) $r['c'];
            }
        } catch (\Throwable $e) {
        }
        return $out;
    }

    /** Count of tickets still needing attention (open/in-progress/on-hold). */
    public static function openCount(): int
    {
        try {
            $db = static::getDB();
            return (int) $db->query("SELECT COUNT(*) FROM isu_tickets
                                     WHERE status IN ('open','in_progress','on_hold')")->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Update status / priority / assignee. Validates enums.
     */
    public static function updateMeta($id, string $status, string $priority, ?string $assigned): bool
    {
        if (!array_key_exists($status, self::statuses()) || !array_key_exists($priority, self::priorities())) {
            return false;
        }
        try {
            $db = static::getDB();
            $resolved = in_array($status, ['resolved', 'closed'], true);
            $sql = 'UPDATE isu_tickets
                    SET status = :s, priority = :p, assigned_to = :a, updated_at = NOW(),
                        resolved_at = ' . ($resolved ? 'COALESCE(resolved_at, NOW())' : 'NULL') . '
                    WHERE id = :id';
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':s', $status, PDO::PARAM_STR);
            $stmt->bindValue(':p', $priority, PDO::PARAM_STR);
            $stmt->bindValue(':a', ($assigned === '' ? null : $assigned));
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /* ------------------------------ replies ------------------------------ */

    public static function addReply($ticketId, array $author, string $body): bool
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare('INSERT INTO isu_ticket_replies (ticket_id, author_id, author_name, body, created_at)
                                  VALUES (:t, :aid, :aname, :body, NOW())');
            $stmt->bindValue(':t', (int) $ticketId, PDO::PARAM_INT);
            $stmt->bindValue(':aid', $author['id'] ?? null, ($author['id'] ?? null) === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':aname', $author['username'] ?? ($author['email'] ?? null));
            $stmt->bindValue(':body', $body, PDO::PARAM_STR);
            $stmt->execute();
            // touch the ticket
            $db->prepare('UPDATE isu_tickets SET updated_at = NOW() WHERE id = :id')->execute([':id' => (int) $ticketId]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function replies($ticketId): array
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare('SELECT * FROM isu_ticket_replies WHERE ticket_id = :t ORDER BY created_at ASC, id ASC');
            $stmt->bindValue(':t', (int) $ticketId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
