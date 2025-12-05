#!/usr/bin/env php
<?php
/**
 * Automated Database Backup Script
 *
 * Creates compressed MySQL backups with intelligent retention policy
 * - Daily: Keep last 7 days
 * - Weekly: Keep last 4 weeks (Sunday backups)
 * - Monthly: Keep last 3 months (1st of month backups)
 *
 * Usage:
 *   php scripts/database-backup.php
 *
 * Cron (daily at 2:00 AM):
 *   0 2 * * * cd /path/to/umdoni-website && php scripts/database-backup.php >> logs/backup.log 2>&1
 *
 * @author Nhlanhla Mnyandu
 * @date 2025-12-04
 */

// Set timezone to South Africa (SAST - UTC+2)
date_default_timezone_set('Africa/Johannesburg');

// Load configuration
require_once __DIR__ . '/../App/Config.php';

use App\Config;

// Configuration
$config = [
    'db_host' => Config::DB_HOST,
    'db_name' => Config::DB_NAME,
    'db_user' => Config::DB_USER,
    'db_password' => Config::DB_PASSWORD,
    'backup_dir' => __DIR__ . '/../backups/database',
    'retention' => [
        'daily' => 7,      // Keep last 7 days
        'weekly' => 4,     // Keep last 4 weeks
        'monthly' => 3     // Keep last 3 months
    ]
];

// Ensure backup directory exists
if (!is_dir($config['backup_dir'])) {
    mkdir($config['backup_dir'], 0755, true);
}

// Generate timestamp and filename
$timestamp = date('Y-m-d_H-i-s');
$year = date('Y');
$month = date('m');
$dayOfWeek = date('w'); // 0 = Sunday
$dayOfMonth = date('d');

// Create year/month directory structure
$targetDir = $config['backup_dir'] . '/' . $year . '/' . $month;
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$filename = "umdoni_backup_{$timestamp}.sql";
$filepath = $targetDir . '/' . $filename;
$gzFilepath = $filepath . '.gz';

echo "=====================================\n";
echo "Database Backup Starting\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "=====================================\n\n";

// Step 1: Create SQL dump
echo "[1/4] Creating database dump...\n";

$command = sprintf(
    'mysqldump --host=%s --user=%s --password=%s --single-transaction --quick --lock-tables=false %s > %s 2>&1',
    escapeshellarg($config['db_host']),
    escapeshellarg($config['db_user']),
    escapeshellarg($config['db_password']),
    escapeshellarg($config['db_name']),
    escapeshellarg($filepath)
);

exec($command, $output, $returnCode);

if ($returnCode !== 0 || !file_exists($filepath)) {
    echo "ERROR: Database dump failed!\n";
    echo "Output: " . implode("\n", $output) . "\n";
    logBackupError("Database dump failed: " . implode(", ", $output));
    exit(1);
}

$dumpSize = filesize($filepath);
echo "✓ Dump created: " . formatBytes($dumpSize) . "\n\n";

// Step 2: Compress the dump
echo "[2/4] Compressing backup...\n";

$gzCommand = sprintf('gzip -9 %s', escapeshellarg($filepath));
exec($gzCommand, $gzOutput, $gzReturnCode);

if ($gzReturnCode !== 0 || !file_exists($gzFilepath)) {
    echo "ERROR: Compression failed!\n";
    logBackupError("Compression failed");
    exit(1);
}

$compressedSize = filesize($gzFilepath);
$compressionRatio = round((1 - ($compressedSize / $dumpSize)) * 100, 2);
echo "✓ Compressed: " . formatBytes($compressedSize) . " (saved {$compressionRatio}%)\n\n";

// Step 3: Apply retention policy
echo "[3/4] Applying retention policy...\n";
$deletedCount = applyRetentionPolicy($config);
echo "✓ Cleaned up {$deletedCount} old backup(s)\n\n";

// Step 4: Log success
echo "[4/4] Logging backup...\n";
logBackupSuccess($gzFilepath, $compressedSize);
echo "✓ Backup logged to database\n\n";

echo "=====================================\n";
echo "Backup Complete!\n";
echo "File: {$gzFilepath}\n";
echo "Size: " . formatBytes($compressedSize) . "\n";
echo "=====================================\n";

exit(0);

// ===== HELPER FUNCTIONS =====

/**
 * Apply retention policy - keep daily, weekly, monthly backups
 */
function applyRetentionPolicy($config) {
    $deleted = 0;
    $backupDir = $config['backup_dir'];

    // Get all backup files recursively
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($backupDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    $backups = [];
    foreach ($iterator as $file) {
        if ($file->isFile() && preg_match('/umdoni_backup_.*\.sql\.gz$/', $file->getFilename())) {
            $backups[] = [
                'path' => $file->getPathname(),
                'time' => $file->getMTime(),
                'date' => date('Y-m-d', $file->getMTime())
            ];
        }
    }

    // Sort by time (newest first)
    usort($backups, function($a, $b) {
        return $b['time'] - $a['time'];
    });

    $now = time();
    $keep = [];

    // Categorize backups
    foreach ($backups as $backup) {
        $age = ($now - $backup['time']) / 86400; // Age in days
        $dayOfWeek = date('w', $backup['time']);
        $dayOfMonth = date('d', $backup['time']);

        // Keep daily backups (last 7 days)
        if ($age <= $config['retention']['daily']) {
            $keep[] = $backup['path'];
            continue;
        }

        // Keep weekly backups (Sunday, last 4 weeks)
        if ($dayOfWeek == 0 && $age <= ($config['retention']['weekly'] * 7)) {
            $keep[] = $backup['path'];
            continue;
        }

        // Keep monthly backups (1st of month, last 3 months)
        if ($dayOfMonth == '01' && $age <= ($config['retention']['monthly'] * 30)) {
            $keep[] = $backup['path'];
            continue;
        }
    }

    // Delete backups not in keep list
    foreach ($backups as $backup) {
        if (!in_array($backup['path'], $keep)) {
            if (unlink($backup['path'])) {
                $deleted++;
                echo "  - Deleted: " . basename($backup['path']) . " (age: " . round(($now - $backup['time']) / 86400, 1) . " days)\n";
            }
        }
    }

    // Clean up empty directories
    cleanupEmptyDirs($backupDir);

    return $deleted;
}

/**
 * Remove empty year/month directories
 */
function cleanupEmptyDirs($path) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isDir()) {
            $dirPath = $file->getPathname();
            if (count(scandir($dirPath)) == 2) { // Only . and ..
                @rmdir($dirPath);
            }
        }
    }
}

/**
 * Log successful backup to database
 */
function logBackupSuccess($filepath, $size) {
    try {
        $db = new PDO(
            'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8mb4',
            Config::DB_USER,
            Config::DB_PASSWORD,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $db->prepare(
            "INSERT INTO logs (userId, username, email, time_log, status, actions, location)
             VALUES (:userId, :username, :email, :time_log, :status, :actions, :location)"
        );

        $stmt->execute([
            'userId' => 'system',
            'username' => 'Database Backup',
            'email' => 'system@umdoni.gov.za',
            'time_log' => date('Y-m-d H:i:s'),
            'status' => 'info',
            'actions' => sprintf('Database backup completed successfully. File: %s, Size: %s',
                                basename($filepath), formatBytes($size)),
            'location' => 'Automated Backup Script'
        ]);

    } catch (Exception $e) {
        echo "Warning: Could not log to database: " . $e->getMessage() . "\n";
    }
}

/**
 * Log backup error to database
 */
function logBackupError($message) {
    try {
        $db = new PDO(
            'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8mb4',
            Config::DB_USER,
            Config::DB_PASSWORD,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $db->prepare(
            "INSERT INTO logs (userId, username, email, time_log, status, actions, location)
             VALUES (:userId, :username, :email, :time_log, :status, :actions, :location)"
        );

        $stmt->execute([
            'userId' => 'system',
            'username' => 'Database Backup',
            'email' => 'system@umdoni.gov.za',
            'time_log' => date('Y-m-d H:i:s'),
            'status' => 'error',
            'actions' => 'Database backup failed: ' . $message,
            'location' => 'Automated Backup Script'
        ]);

    } catch (Exception $e) {
        echo "Warning: Could not log error to database: " . $e->getMessage() . "\n";
    }
}

/**
 * Format bytes to human readable
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
