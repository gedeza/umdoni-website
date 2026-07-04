<?php

namespace App\Controllers\Isu;

use App\Models\SiteControl;
use Core\View;

/**
 * Console — ISU Technologies provider control panel.
 *
 * URL base: /isu/console
 *   /isu/console/index    Dashboard: current status + action + history
 *   /isu/console/suspend  (POST) Take the site down (service kill-switch)
 *   /isu/console/restore  (POST) Bring the site back up
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class Console extends Guarded
{
    public function indexAction()
    {
        View::render('isu/console/index.php', [
            'suspended'  => SiteControl::isSuspended(),
            'info'       => SiteControl::suspensionInfo(),
            'history'    => SiteControl::history(25),
            'csrf_token' => $this->csrfToken(),
            'isuUser'    => $this->isuUser,
            'flash'      => $this->takeFlash(),
        ], 'isu');
    }

    public function suspendAction()
    {
        if (!$this->isPost() || !$this->validCsrf()) {
            $this->flash('error', 'Invalid or expired request. Please try again.');
            redirect('isu/console/index');
            return;
        }

        if (SiteControl::isSuspended()) {
            $this->flash('info', 'The site is already suspended.');
            redirect('isu/console/index');
            return;
        }

        $reason = trim($_POST['reason'] ?? '');
        $reason = $reason === '' ? null : mb_substr($reason, 0, 255);

        if (SiteControl::suspend($this->isuUser, $reason)) {
            $this->flash('success', 'Service SUSPENDED. The public site is now offline.');
        } else {
            $this->flash('error', 'Could not suspend the site (check storage/ is writable).');
        }
        redirect('isu/console/index');
    }

    public function restoreAction()
    {
        if (!$this->isPost() || !$this->validCsrf()) {
            $this->flash('error', 'Invalid or expired request. Please try again.');
            redirect('isu/console/index');
            return;
        }

        if (!SiteControl::isSuspended()) {
            $this->flash('info', 'The site is already live.');
            redirect('isu/console/index');
            return;
        }

        $reason = trim($_POST['reason'] ?? '');
        $reason = $reason === '' ? null : mb_substr($reason, 0, 255);

        if (SiteControl::restore($this->isuUser, $reason)) {
            $this->flash('success', 'Service RESTORED. The site is live again.');
        } else {
            $this->flash('error', 'Could not restore the site (check storage/ permissions).');
        }
        redirect('isu/console/index');
    }

    /* ------------------------------------------------------------------ */

    private function isPost(): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['isu_flash'] = ['type' => $type, 'message' => $message];
    }

    private function takeFlash(): ?array
    {
        $flash = $_SESSION['isu_flash'] ?? null;
        unset($_SESSION['isu_flash']);
        return $flash;
    }
}
