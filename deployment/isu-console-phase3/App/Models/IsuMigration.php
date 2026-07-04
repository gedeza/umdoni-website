<?php

namespace App\Models;

use PDO;

/**
 * IsuMigration
 *
 * Runs PRE-APPROVED SQL migration files shipped in /migrations. There is no
 * free-form SQL: only files present on disk can be executed, each exactly
 * once (tracked in isu_migrations). Intended to replace hand-pasting SQL in
 * phpMyAdmin for future schema changes.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class IsuMigration extends \Core\Repository
{
    public static function dir(): string
    {
        return dirname(__DIR__, 2) . '/migrations';
    }

    /**
     * Available migration files on disk (sorted by name).
     * @return string[] filenames
     */
    public static function available(): array
    {
        $dir = self::dir();
        if (!is_dir($dir)) {
            return [];
        }
        $files = glob($dir . '/*.sql') ?: [];
        $names = array_map('basename', $files);
        sort($names, SORT_STRING);
        return $names;
    }

    /**
     * Filenames already run (from the tracking table).
     * @return array<string,string> filename => ran_at
     */
    public static function ranMap(): array
    {
        try {
            $db = static::getDB();
            $rows = $db->query('SELECT filename, ran_at, actor_name FROM isu_migrations')
                       ->fetchAll(PDO::FETCH_ASSOC);
            $map = [];
            foreach ($rows as $r) {
                $map[$r['filename']] = $r;
            }
            return $map;
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Combined status list: each available file with run info.
     */
    public static function status(): array
    {
        $ran = self::ranMap();
        $out = [];
        foreach (self::available() as $name) {
            $out[] = [
                'filename' => $name,
                'ran'      => isset($ran[$name]),
                'ran_at'   => $ran[$name]['ran_at'] ?? null,
                'ran_by'   => $ran[$name]['actor_name'] ?? null,
            ];
        }
        return $out;
    }

    /**
     * Execute a pending migration file, once.
     *
     * @param array $actor ['id'=>, 'username'=>, 'email'=>]
     * @return array ['ok'=>bool, 'message'=>string]
     */
    public static function run(string $filename, array $actor): array
    {
        $filename = basename($filename); // traversal-safe
        if (!preg_match('/^[A-Za-z0-9._-]+\.sql$/', $filename)) {
            return ['ok' => false, 'message' => 'Invalid migration filename.'];
        }
        $path = self::dir() . '/' . $filename;
        if (!is_file($path)) {
            return ['ok' => false, 'message' => 'Migration file not found on disk.'];
        }

        // Refuse to run twice.
        $ran = self::ranMap();
        if (isset($ran[$filename])) {
            return ['ok' => false, 'message' => 'That migration has already been run.'];
        }

        $sql = file_get_contents($path);
        if ($sql === false || trim($sql) === '') {
            return ['ok' => false, 'message' => 'Migration file is empty or unreadable.'];
        }

        $statements = self::splitStatements($sql);
        if (empty($statements)) {
            return ['ok' => false, 'message' => 'No SQL statements found in the migration.'];
        }

        try {
            $db = static::getDB();
            // Note: MySQL implicitly commits DDL, so a wrapping transaction
            // can't roll back schema changes. We run sequentially and stop on
            // the first error, recording the migration only on full success.
            foreach ($statements as $i => $stmt) {
                try {
                    $db->exec($stmt);
                } catch (\Throwable $e) {
                    return [
                        'ok' => false,
                        'message' => 'Statement ' . ($i + 1) . ' failed: ' . $e->getMessage()
                            . ' — migration NOT recorded; fix and re-run.',
                    ];
                }
            }

            $ins = $db->prepare(
                'INSERT INTO isu_migrations (filename, ran_at, ran_by, actor_name)
                 VALUES (:f, NOW(), :by, :name)'
            );
            $ins->bindValue(':f', $filename, PDO::PARAM_STR);
            $ins->bindValue(':by', $actor['id'] ?? null, ($actor['id'] ?? null) === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $ins->bindValue(':name', $actor['username'] ?? ($actor['email'] ?? null));
            $ins->execute();

            return ['ok' => true, 'message' => $filename . ' ran successfully (' . count($statements) . ' statement(s)).'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Migration error: ' . $e->getMessage()];
        }
    }

    /**
     * Split a SQL file into individual statements. Handles `-- ` line
     * comments and blank lines. (Migration files are authored/reviewed in
     * the repo, so no stored-procedure DELIMITER handling is needed.)
     *
     * @return string[]
     */
    private static function splitStatements(string $sql): array
    {
        // Strip full-line comments (-- ... and # ...).
        $sql = preg_replace('/^\s*(--|#).*$/m', '', $sql);
        $parts = explode(';', $sql);
        $statements = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p !== '') {
                $statements[] = $p;
            }
        }
        return $statements;
    }
}
