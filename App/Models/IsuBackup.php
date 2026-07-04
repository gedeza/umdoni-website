<?php

namespace App\Models;

/**
 * IsuBackup
 *
 * Thin wrapper around the project's existing backup engine
 * (scripts/database-backup.php, storing gzipped dumps under
 * backups/database/YYYY/MM/). Surfaces create / list / download / delete
 * for the ISU console.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class IsuBackup
{
    private static function root(): string
    {
        return dirname(__DIR__, 2);
    }

    public static function dir(): string
    {
        return self::root() . '/backups/database';
    }

    private static function scriptPath(): string
    {
        return self::root() . '/scripts/database-backup.php';
    }

    /**
     * Create a backup by invoking the existing backup script.
     *
     * @return array ['ok' => bool, 'message' => string]
     */
    public static function create(): array
    {
        $script = self::scriptPath();
        if (!is_file($script)) {
            return ['ok' => false, 'message' => 'Backup script not found on the server.'];
        }
        if (!function_exists('exec')) {
            return ['ok' => false, 'message' => 'exec() is disabled on this server; cannot run the backup.'];
        }

        $output = [];
        $rc = 0;
        exec('php ' . escapeshellarg($script) . ' 2>&1', $output, $rc);

        if ($rc === 0) {
            return ['ok' => true, 'message' => 'Database backup created successfully.'];
        }
        $tail = trim(implode("\n", array_slice($output, -3)));
        return ['ok' => false, 'message' => 'Backup failed. ' . $tail];
    }

    /**
     * List all backup files (newest first).
     */
    public static function all(): array
    {
        $dir = self::dir();
        $backups = [];
        if (!is_dir($dir)) {
            return $backups;
        }
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->isFile() && preg_match('/umdoni_backup_.*\.sql\.gz$/', $file->getFilename())) {
                $backups[] = [
                    'filename'  => $file->getFilename(),
                    'size'      => $file->getSize(),
                    'size_h'    => self::humanSize($file->getSize()),
                    'date'      => date('Y-m-d H:i:s', $file->getMTime()),
                    'timestamp' => $file->getMTime(),
                ];
            }
        }
        usort($backups, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        return $backups;
    }

    /**
     * Resolve a backup filename to an absolute path (traversal-safe).
     */
    public static function find(string $filename): ?string
    {
        $filename = basename($filename); // prevent directory traversal
        if (!preg_match('/^umdoni_backup_.*\.sql\.gz$/', $filename)) {
            return null;
        }
        $dir = self::dir();
        if (!is_dir($dir)) {
            return null;
        }
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if ($file->isFile() && $file->getFilename() === $filename) {
                return $file->getPathname();
            }
        }
        return null;
    }

    public static function delete(string $filename): bool
    {
        $path = self::find($filename);
        return $path && is_file($path) && @unlink($path);
    }

    private static function humanSize($bytes): string
    {
        $bytes = (float) $bytes;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
