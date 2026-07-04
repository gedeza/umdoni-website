<?php

namespace App\Models;

use PDO;
use ZipArchive;

/**
 * IsuPatch
 *
 * Constrained "deploy a patch ZIP from the browser" engine.
 *
 * Safety model:
 *   - Entries may only land under whitelisted top folders (App/, public/,
 *     Components/, migrations/).
 *   - Path traversal, absolute paths, protected files (App/Config.php, .env,
 *     .htaccess, vendor/) and disallowed extensions are rejected.
 *   - A patch is applied ONLY if every entry passes; nothing is applied
 *     partially by surprise.
 *   - Overwritten files are backed up first; a manifest is written so the
 *     whole patch can be rolled back with one click.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class IsuPatch extends \Core\Repository
{
    const MAX_BYTES  = 20971520; // 20 MB
    const MAX_FILES  = 500;

    /** Allowed top-level path prefixes. */
    private static $allowedPrefixes = ['App/', 'public/', 'Components/', 'migrations/'];

    /** Allowed file extensions. */
    private static $allowedExt = [
        'php', 'css', 'js', 'map', 'sql', 'md', 'txt', 'json',
        'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp',
        'woff', 'woff2', 'ttf', 'eot',
    ];

    /** Exact paths / prefixes that must never be written. */
    private static $blocked = ['App/Config.php', 'vendor/', '.env', '.htaccess'];

    private static function root(): string
    {
        return dirname(__DIR__, 2);
    }

    private static function baseDir(): string
    {
        return self::root() . '/storage/isu-patches';
    }

    /* ----------------------------- staging ----------------------------- */

    /**
     * Validate and stage an uploaded ZIP. Returns ['ok'=>bool,'token'|'message'].
     */
    public static function stage(array $file): array
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'message' => 'Upload failed. Please try again.'];
        }
        if ($file['size'] <= 0 || $file['size'] > self::MAX_BYTES) {
            return ['ok' => false, 'message' => 'ZIP must be between 1 byte and 20 MB.'];
        }
        if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'zip') {
            return ['ok' => false, 'message' => 'Please upload a .zip file.'];
        }
        if (!class_exists('ZipArchive')) {
            return ['ok' => false, 'message' => 'The ZipArchive extension is not available on this server.'];
        }

        $incoming = self::baseDir() . '/incoming';
        if (!is_dir($incoming) && !@mkdir($incoming, 0755, true)) {
            return ['ok' => false, 'message' => 'Cannot create staging directory (check storage/ permissions).'];
        }

        $token = bin2hex(random_bytes(16));
        $dest  = $incoming . '/' . $token . '.zip';
        if (!@move_uploaded_file($file['tmp_name'], $dest)) {
            return ['ok' => false, 'message' => 'Could not stage the uploaded file.'];
        }

        // Quick sanity: openable and within file-count limit.
        $zip = new ZipArchive();
        if ($zip->open($dest) !== true) {
            @unlink($dest);
            return ['ok' => false, 'message' => 'That file is not a valid ZIP archive.'];
        }
        $count = $zip->numFiles;
        $zip->close();
        if ($count > self::MAX_FILES) {
            @unlink($dest);
            return ['ok' => false, 'message' => 'ZIP has too many files (limit ' . self::MAX_FILES . ').'];
        }

        return ['ok' => true, 'token' => $token, 'original' => basename($file['name'])];
    }

    public static function incomingPath(string $token): ?string
    {
        $token = preg_replace('/[^a-f0-9]/', '', $token);
        if ($token === '') {
            return null;
        }
        $p = self::baseDir() . '/incoming/' . $token . '.zip';
        return is_file($p) ? $p : null;
    }

    public static function discard(string $token): void
    {
        $p = self::incomingPath($token);
        if ($p) {
            @unlink($p);
        }
    }

    /* ---------------------------- inspection --------------------------- */

    /**
     * List the ZIP's entries with per-entry allow/block + action.
     * Returns ['ok'=>bool, 'entries'=>[...], 'all_ok'=>bool, 'message'?].
     */
    public static function inspect(string $token): array
    {
        $path = self::incomingPath($token);
        if (!$path) {
            return ['ok' => false, 'message' => 'Staged patch not found (it may have expired).'];
        }
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return ['ok' => false, 'message' => 'Could not open the staged ZIP.'];
        }

        $entries = [];
        $allOk = true;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name === false || substr($name, -1) === '/') {
                continue; // skip directory entries
            }
            $verdict = self::verify($name);
            if (!$verdict['ok']) {
                $allOk = false;
            }
            $entries[] = [
                'name'    => $name,
                'ok'      => $verdict['ok'],
                'reason'  => $verdict['reason'],
                'action'  => is_file(self::root() . '/' . $name) ? 'overwrite' : 'new',
            ];
        }
        $zip->close();

        if (empty($entries)) {
            return ['ok' => false, 'message' => 'The ZIP contains no files.'];
        }
        return ['ok' => true, 'entries' => $entries, 'all_ok' => $allOk];
    }

    /**
     * Whitelist/blacklist check for a single entry path.
     */
    private static function verify(string $name): array
    {
        if ($name === '' || strpos($name, '..') !== false || strpos($name, "\0") !== false
            || $name[0] === '/' || strpos($name, '\\') !== false) {
            return ['ok' => false, 'reason' => 'unsafe path'];
        }
        foreach (self::$blocked as $b) {
            if ($name === $b || strpos($name, $b) === 0 || basename($name) === $b) {
                return ['ok' => false, 'reason' => 'protected file'];
            }
        }
        $inAllowed = false;
        foreach (self::$allowedPrefixes as $p) {
            if (strpos($name, $p) === 0) {
                $inAllowed = true;
                break;
            }
        }
        if (!$inAllowed) {
            return ['ok' => false, 'reason' => 'outside allowed folders'];
        }
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, self::$allowedExt, true)) {
            return ['ok' => false, 'reason' => 'file type not allowed'];
        }
        return ['ok' => true, 'reason' => ''];
    }

    /* ------------------------------ apply ------------------------------ */

    /**
     * Apply a fully-valid staged patch: back up overwrites, extract files,
     * write a manifest, and record the patch for rollback.
     *
     * @param array $actor ['id'=>,'username'=>,'email'=>]
     */
    public static function apply(string $token, ?string $original, array $actor): array
    {
        $inspect = self::inspect($token);
        if (!$inspect['ok']) {
            return ['ok' => false, 'message' => $inspect['message']];
        }
        if (empty($inspect['all_ok'])) {
            return ['ok' => false, 'message' => 'Patch has blocked entries; nothing was applied.'];
        }

        $path = self::incomingPath($token);
        $zip  = new ZipArchive();
        if (!$path || $zip->open($path) !== true) {
            return ['ok' => false, 'message' => 'Could not open the staged ZIP.'];
        }

        $backupRel = 'storage/isu-patches/backups/' . $token;
        $backupAbs = self::root() . '/' . $backupRel;
        if (!is_dir($backupAbs) && !@mkdir($backupAbs, 0755, true)) {
            $zip->close();
            return ['ok' => false, 'message' => 'Cannot create backup directory (check storage/ permissions).'];
        }

        $manifest = ['overwritten' => [], 'added' => []];
        $applied = 0;

        foreach ($inspect['entries'] as $entry) {
            $name = $entry['name'];
            $dest = self::root() . '/' . $name;
            $data = $zip->getFromName($name);
            if ($data === false) {
                continue;
            }
            $destDir = dirname($dest);
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            // Back up an existing file before overwriting.
            if (is_file($dest)) {
                $bDest = $backupAbs . '/' . $name;
                if (!is_dir(dirname($bDest))) {
                    @mkdir(dirname($bDest), 0755, true);
                }
                @copy($dest, $bDest);
                $manifest['overwritten'][] = $name;
            } else {
                $manifest['added'][] = $name;
            }
            if (@file_put_contents($dest, $data) !== false) {
                $applied++;
            }
        }
        $zip->close();

        @file_put_contents($backupAbs . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));

        // Record for rollback.
        try {
            $db = static::getDB();
            $stmt = $db->prepare(
                'INSERT INTO isu_patches (token, original_name, file_count, backup_dir, status, applied_by, actor_name)
                 VALUES (:t, :o, :c, :d, "applied", :by, :name)'
            );
            $stmt->bindValue(':t', $token, PDO::PARAM_STR);
            $stmt->bindValue(':o', $original, $original === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':c', $applied, PDO::PARAM_INT);
            $stmt->bindValue(':d', $backupRel, PDO::PARAM_STR);
            $stmt->bindValue(':by', $actor['id'] ?? null, ($actor['id'] ?? null) === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindValue(':name', $actor['username'] ?? ($actor['email'] ?? null));
            $stmt->execute();
        } catch (\Throwable $e) {
            // Files are applied and backed up even if the DB record fails.
        }

        // Staged zip no longer needed.
        @unlink($path);

        return [
            'ok' => true,
            'message' => "Applied {$applied} file(s). "
                . count($manifest['overwritten']) . ' overwritten, '
                . count($manifest['added']) . ' new. You can roll this back below.',
        ];
    }

    /* ----------------------------- rollback ---------------------------- */

    public static function recent(int $limit = 15): array
    {
        try {
            $db = static::getDB();
            $limit = max(1, min(50, $limit));
            $stmt = $db->prepare('SELECT * FROM isu_patches ORDER BY applied_at DESC, id DESC LIMIT ' . $limit);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function rollback($id, array $actor): array
    {
        try {
            $db = static::getDB();
            $stmt = $db->prepare('SELECT * FROM isu_patches WHERE id = :id LIMIT 1');
            $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
            $stmt->execute();
            $patch = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Could not load that patch.'];
        }

        if (!$patch) {
            return ['ok' => false, 'message' => 'Patch not found.'];
        }
        if ($patch['status'] === 'rolled_back') {
            return ['ok' => false, 'message' => 'That patch is already rolled back.'];
        }

        $backupAbs = self::root() . '/' . $patch['backup_dir'];
        $manifestF = $backupAbs . '/manifest.json';
        if (!is_file($manifestF)) {
            return ['ok' => false, 'message' => 'Backup manifest missing; cannot roll back safely.'];
        }
        $manifest = json_decode((string) file_get_contents($manifestF), true);
        if (!is_array($manifest)) {
            return ['ok' => false, 'message' => 'Backup manifest is unreadable.'];
        }

        $restored = 0;
        $removed = 0;
        foreach (($manifest['overwritten'] ?? []) as $name) {
            $src  = $backupAbs . '/' . $name;
            $dest = self::root() . '/' . $name;
            if (is_file($src) && @copy($src, $dest)) {
                $restored++;
            }
        }
        foreach (($manifest['added'] ?? []) as $name) {
            $dest = self::root() . '/' . $name;
            if (is_file($dest) && @unlink($dest)) {
                $removed++;
            }
        }

        try {
            $db->prepare('UPDATE isu_patches SET status = "rolled_back", rolled_back_at = NOW() WHERE id = :id')
               ->execute([':id' => (int) $id]);
        } catch (\Throwable $e) {
            // best effort
        }

        return ['ok' => true, 'message' => "Rolled back: {$restored} restored, {$removed} removed."];
    }
}
