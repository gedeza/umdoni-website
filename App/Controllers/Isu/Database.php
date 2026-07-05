<?php

namespace App\Controllers\Isu;

use App\Models\IsuBackup;
use App\Models\IsuMigration;
use App\Models\IsuAudit;
use Core\View;

/**
 * Database — ISU console DB tools (Phase 3).
 *
 *   /isu/database/index          backups list + create + migrations status
 *   /isu/database/backup         (POST) create a backup now
 *   /isu/database/download?file= (GET)  download a backup (traversal-safe)
 *   /isu/database/deletebackup   (POST) delete a backup
 *   /isu/database/runmigration   (POST) run ONE pre-approved migration file
 *
 * No free-form SQL: migrations can only be run from vetted files in
 * /migrations, each exactly once.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class Database extends Guarded
{
    public function indexAction()
    {
        View::render('isu/database/index.php', [
            'page_title' => 'Database',
            'page_desc'  => 'Create and download backups, and run pre-approved database migrations.',
            'backups'    => IsuBackup::all(),
            'migrations' => IsuMigration::status(),
            'csrf_token' => $this->csrfToken(),
            'flash'      => $this->takeFlash(),
        ], 'isu');
    }

    public function backupAction()
    {
        if (!$this->guardPost()) return;

        $res = IsuBackup::create();
        if ($res['ok']) {
            IsuAudit::log('db.backup', 'Created a database backup', $this->me());
            $this->flash('success', $res['message']);
        } else {
            $this->flash('error', $res['message']);
        }
        redirect('isu/database/index');
    }

    public function downloadAction()
    {
        $filename = basename($_GET['file'] ?? '');
        $path = $filename !== '' ? IsuBackup::find($filename) : null;
        if (!$path) {
            $this->flash('error', 'Backup file not found.');
            redirect('isu/database/index');
            return;
        }

        IsuAudit::log('db.backup_download', 'Downloaded ' . $filename, $this->me());
        header('Content-Type: application/gzip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    public function deletebackupAction()
    {
        if (!$this->guardPost()) return;

        $filename = basename($_POST['file'] ?? '');
        if ($filename !== '' && IsuBackup::delete($filename)) {
            IsuAudit::log('db.backup_delete', 'Deleted ' . $filename, $this->me());
            $this->flash('success', 'Backup deleted.');
        } else {
            $this->flash('error', 'Could not delete that backup.');
        }
        redirect('isu/database/index');
    }

    public function runmigrationAction()
    {
        if (!$this->guardPost()) return;

        $filename = basename($_POST['file'] ?? '');
        $res = IsuMigration::run($filename, $this->me());
        if ($res['ok']) {
            IsuAudit::log('db.migration_run', 'Ran migration ' . $filename, $this->me());
            $this->flash('success', $res['message']);
        } else {
            $this->flash('error', $res['message']);
        }
        redirect('isu/database/index');
    }

    /* ------------------------------------------------------------------ */

    private function me(): array
    {
        return [
            'id'       => $this->isuUser['id'] ?? null,
            'username' => $this->isuUser['username'] ?? null,
            'email'    => $this->isuUser['email'] ?? null,
        ];
    }

    private function guardPost(): bool
    {
        $isPost = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
        if (!$isPost || !$this->validCsrf()) {
            $this->flash('error', 'Invalid or expired request. Please try again.');
            redirect('isu/database/index');
            return false;
        }
        return true;
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['isu_db_flash'] = ['type' => $type, 'message' => $message];
    }

    private function takeFlash(): ?array
    {
        $f = $_SESSION['isu_db_flash'] ?? null;
        unset($_SESSION['isu_db_flash']);
        return $f;
    }
}
