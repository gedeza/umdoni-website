<?php

namespace App\Controllers\Isu;

use App\Models\IsuPatch;
use App\Models\IsuAudit;
use Core\View;

/**
 * Deploy — constrained patch-ZIP deploy tool (Phase 4).
 *
 *   /isu/deploy/index          upload form + recent patches (with rollback)
 *   /isu/deploy/upload         (POST) stage a ZIP, go to review
 *   /isu/deploy/review?token=  (GET)  show the file list + apply/discard
 *   /isu/deploy/apply          (POST) apply a fully-valid staged patch
 *   /isu/deploy/discard        (POST) throw away a staged patch
 *   /isu/deploy/rollback       (POST) roll back an applied patch
 *
 * Files may only land under whitelisted folders; protected files are refused;
 * overwritten files are backed up for one-click rollback. See App\Models\IsuPatch.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class Deploy extends Guarded
{
    public function indexAction()
    {
        View::render('isu/deploy/index.php', [
            'page_title' => 'Deploy',
            'page_desc'  => 'Upload a code patch, review it, apply it, and roll back if needed.',
            'patches'    => IsuPatch::recent(15),
            'csrf_token' => $this->csrfToken(),
            'flash'      => $this->takeFlash(),
        ], 'isu');
    }

    public function uploadAction()
    {
        if (!$this->guardPost()) return;

        $res = IsuPatch::stage($_FILES['patch'] ?? []);
        if (!$res['ok']) {
            $this->flash('error', $res['message']);
            redirect('isu/deploy/index');
            return;
        }
        // Remember the original name for the review/apply step.
        $_SESSION['isu_patch_original'] = $res['original'] ?? null;
        redirect('isu/deploy/review?token=' . urlencode($res['token']));
    }

    public function reviewAction()
    {
        $token = preg_replace('/[^a-f0-9]/', '', $_GET['token'] ?? '');
        $inspect = IsuPatch::inspect($token);
        if (!$inspect['ok']) {
            $this->flash('error', $inspect['message']);
            redirect('isu/deploy/index');
            return;
        }
        View::render('isu/deploy/review.php', [
            'page_title' => 'Deploy — review patch',
            'page_desc'  => 'Check the file list below, then apply or discard.',
            'token'      => $token,
            'original'   => $_SESSION['isu_patch_original'] ?? null,
            'entries'    => $inspect['entries'],
            'all_ok'     => $inspect['all_ok'],
            'csrf_token' => $this->csrfToken(),
        ], 'isu');
    }

    public function applyAction()
    {
        if (!$this->guardPost()) return;

        $token    = preg_replace('/[^a-f0-9]/', '', $_POST['token'] ?? '');
        $original = $_SESSION['isu_patch_original'] ?? null;

        $res = IsuPatch::apply($token, $original, $this->me());
        unset($_SESSION['isu_patch_original']);

        if ($res['ok']) {
            IsuAudit::log('deploy.apply', 'Applied patch ' . ($original ?? $token), $this->me());
            $this->flash('success', $res['message']);
        } else {
            $this->flash('error', $res['message']);
        }
        redirect('isu/deploy/index');
    }

    public function discardAction()
    {
        if (!$this->guardPost()) return;

        $token = preg_replace('/[^a-f0-9]/', '', $_POST['token'] ?? '');
        IsuPatch::discard($token);
        unset($_SESSION['isu_patch_original']);
        $this->flash('info', 'Staged patch discarded.');
        redirect('isu/deploy/index');
    }

    public function rollbackAction()
    {
        if (!$this->guardPost()) return;

        $id  = (int) ($_POST['id'] ?? 0);
        $res = IsuPatch::rollback($id, $this->me());
        if ($res['ok']) {
            IsuAudit::log('deploy.rollback', 'Rolled back patch #' . $id, $this->me());
            $this->flash('success', $res['message']);
        } else {
            $this->flash('error', $res['message']);
        }
        redirect('isu/deploy/index');
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
            redirect('isu/deploy/index');
            return false;
        }
        return true;
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['isu_deploy_flash'] = ['type' => $type, 'message' => $message];
    }

    private function takeFlash(): ?array
    {
        $f = $_SESSION['isu_deploy_flash'] ?? null;
        unset($_SESSION['isu_deploy_flash']);
        return $f;
    }
}
