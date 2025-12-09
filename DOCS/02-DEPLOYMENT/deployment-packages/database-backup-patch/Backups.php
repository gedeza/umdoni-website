<?php
/**
 * Dashboard Backups Controller
 *
 * Manages database backup operations from admin dashboard
 *
 * @author Nhlanhla Mnyandu
 * @date 2025-12-04
 */

namespace App\Controllers\Dashboard;

use \Core\View;

class Backups extends \Core\Controller
{
    /**
     * Before filter - require authentication
     */
    protected function before()
    {
        // Ensure user is authenticated
        if (!isset($_SESSION['profile'])) {
            redirect('authentication/login');
            return false;
        }
    }

    /**
     * Backups index page - show backup history and controls
     */
    public function indexAction()
    {
        $backups = $this->getBackupList();
        $stats = $this->getBackupStats($backups);

        View::render('dashboard/backups/index.php', [
            'backups' => $backups,
            'stats' => $stats
        ], 'dashboard');
    }

    /**
     * Trigger manual backup
     */
    public function createAction()
    {
        try {
            $scriptPath = __DIR__ . '/../../../scripts/database-backup.php';

            if (!file_exists($scriptPath)) {
                throw new \Exception('Backup script not found');
            }

            // Execute backup script
            $output = [];
            $returnCode = 0;
            exec("php {$scriptPath} 2>&1", $output, $returnCode);

            if ($returnCode === 0) {
                $_SESSION['success'] = [
                    'message' => 'Database backup created successfully!'
                ];
            } else {
                throw new \Exception('Backup failed: ' . implode("\n", $output));
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = [
                'message' => 'Backup failed: ' . $e->getMessage()
            ];
        }

        redirect('dashboard/backups/index');
    }

    /**
     * Download a backup file
     */
    public function downloadAction()
    {
        if (!isset($_GET['file'])) {
            $_SESSION['error'] = ['message' => 'No backup file specified'];
            redirect('dashboard/backups/index');
            return;
        }

        $filename = basename($_GET['file']); // Security: prevent directory traversal
        $backupDir = __DIR__ . '/../../../backups/database';

        // Find the file recursively
        $filepath = $this->findBackupFile($backupDir, $filename);

        if (!$filepath || !file_exists($filepath)) {
            $_SESSION['error'] = ['message' => 'Backup file not found'];
            redirect('dashboard/backups/index');
            return;
        }

        // Send file for download
        header('Content-Type: application/gzip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }

    /**
     * Delete a backup file
     */
    public function deleteAction()
    {
        if (!isset($_POST['file'])) {
            $_SESSION['error'] = ['message' => 'No backup file specified'];
            redirect('dashboard/backups/index');
            return;
        }

        try {
            $filename = basename($_POST['file']);
            $backupDir = __DIR__ . '/../../../backups/database';
            $filepath = $this->findBackupFile($backupDir, $filename);

            if (!$filepath || !file_exists($filepath)) {
                throw new \Exception('Backup file not found');
            }

            if (unlink($filepath)) {
                $_SESSION['success'] = ['message' => 'Backup deleted successfully'];
            } else {
                throw new \Exception('Failed to delete backup file');
            }

        } catch (\Exception $e) {
            $_SESSION['error'] = ['message' => $e->getMessage()];
        }

        redirect('dashboard/backups/index');
    }

    /**
     * Get list of all backup files
     */
    private function getBackupList()
    {
        $backups = [];
        $backupDir = __DIR__ . '/../../../backups/database';

        if (!is_dir($backupDir)) {
            return $backups;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($backupDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && preg_match('/umdoni_backup_.*\.sql\.gz$/', $file->getFilename())) {
                $backups[] = [
                    'filename' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'size' => $file->getSize(),
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    'timestamp' => $file->getMTime()
                ];
            }
        }

        // Sort by date (newest first)
        usort($backups, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return $backups;
    }

    /**
     * Calculate backup statistics
     */
    private function getBackupStats($backups)
    {
        $totalSize = 0;
        $oldestBackup = null;
        $newestBackup = null;

        foreach ($backups as $backup) {
            $totalSize += $backup['size'];

            if ($oldestBackup === null || $backup['timestamp'] < $oldestBackup) {
                $oldestBackup = $backup['timestamp'];
            }

            if ($newestBackup === null || $backup['timestamp'] > $newestBackup) {
                $newestBackup = $backup['timestamp'];
            }
        }

        return [
            'total_count' => count($backups),
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'oldest_backup' => $oldestBackup ? date('Y-m-d H:i:s', $oldestBackup) : 'N/A',
            'newest_backup' => $newestBackup ? date('Y-m-d H:i:s', $newestBackup) : 'N/A'
        ];
    }

    /**
     * Find backup file recursively
     */
    private function findBackupFile($dir, $filename)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->getFilename() === $filename) {
                return $file->getPathname();
            }
        }

        return null;
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * After filter
     */
    protected function after()
    {
        // Nothing to do
    }
}
