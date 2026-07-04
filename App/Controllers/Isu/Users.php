<?php

namespace App\Controllers\Isu;

use App\Models\IsuAdmin;
use App\Models\IsuAudit;
use Core\View;

/**
 * Users — ISU admin management (Phase 2).
 *
 * URLs (all ISU-gated via Guarded):
 *   /isu/users/index       list admins + add form + recent audit
 *   /isu/users/create      (POST) add an admin (shows a one-time temp password)
 *   /isu/users/reset       (POST) reset an admin's password (one-time temp)
 *   /isu/users/deactivate  (POST) disable an admin (instant access cut)
 *   /isu/users/activate    (POST) re-enable an admin
 *
 * Safeguards: you cannot deactivate yourself or the last active admin.
 * New temp passwords are shown ONCE on screen (never stored in plaintext).
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class Users extends Guarded
{
    public function indexAction()
    {
        View::render('isu/users/index.php', [
            'admins'      => IsuAdmin::getAll(),
            'audit'       => IsuAudit::recent(20),
            'csrf_token'  => $this->csrfToken(),
            'me_id'       => (int) ($this->isuUser['id'] ?? 0),
            'flash'       => $this->takeFlash('isu_users_flash'),
            'new_cred'    => $this->takeFlash('isu_new_cred'),
        ], 'isu');
    }

    public function createAction()
    {
        if (!$this->guardPost()) return;

        $username = trim($_POST['username'] ?? '');
        $email    = strtolower(trim($_POST['email'] ?? ''));

        if ($username === '' || mb_strlen($username) > 100) {
            return $this->back('error', 'Please enter a valid name.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->back('error', 'Please enter a valid email address.');
        }
        if (IsuAdmin::emailExists($email)) {
            return $this->back('error', 'An ISU admin with that email already exists.');
        }

        $temp = $this->generateTempPassword();
        $id   = IsuAdmin::create($username, $email, password_hash($temp, PASSWORD_DEFAULT));

        if (!$id) {
            return $this->back('error', 'Could not create the admin. Please try again.');
        }

        IsuAudit::log('admin.create', 'Created ISU admin ' . $email, $this->me());
        $_SESSION['isu_new_cred'] = [
            'label'    => 'New ISU admin created',
            'email'    => $email,
            'password' => $temp,
        ];
        $this->flash('isu_users_flash', ['type' => 'success', 'message' => 'Admin created. Share the one-time password below securely.']);
        redirect('isu/users/index');
    }

    public function resetAction()
    {
        if (!$this->guardPost()) return;

        $id     = (int) ($_POST['id'] ?? 0);
        $target = IsuAdmin::getById($id);
        if (!$target) {
            return $this->back('error', 'Admin not found.');
        }
        if ($id === (int) $this->isuUser['id']) {
            return $this->back('error', 'To change your own password, use Sign out then Change password, or the change-password screen.');
        }

        $temp = $this->generateTempPassword();
        if (!IsuAdmin::resetPassword($id, password_hash($temp, PASSWORD_DEFAULT))) {
            return $this->back('error', 'Could not reset the password.');
        }

        IsuAudit::log('admin.reset_password', 'Reset password for ' . $target['email'], $this->me());
        $_SESSION['isu_new_cred'] = [
            'label'    => 'Password reset for ' . $target['email'],
            'email'    => $target['email'],
            'password' => $temp,
        ];
        $this->flash('isu_users_flash', ['type' => 'success', 'message' => 'Password reset. Share the one-time password below securely.']);
        redirect('isu/users/index');
    }

    public function deactivateAction()
    {
        if (!$this->guardPost()) return;

        $id     = (int) ($_POST['id'] ?? 0);
        $target = IsuAdmin::getById($id);
        if (!$target) {
            return $this->back('error', 'Admin not found.');
        }
        if ($id === (int) $this->isuUser['id']) {
            return $this->back('error', 'You cannot deactivate your own account.');
        }
        if ((int) $target['active'] === 1 && IsuAdmin::countActive() <= 1) {
            return $this->back('error', 'You cannot deactivate the last active admin.');
        }

        IsuAdmin::setActive($id, false);
        IsuAudit::log('admin.deactivate', 'Deactivated ' . $target['email'], $this->me());
        return $this->back('success', $target['email'] . ' deactivated. Their access is now revoked.');
    }

    public function activateAction()
    {
        if (!$this->guardPost()) return;

        $id     = (int) ($_POST['id'] ?? 0);
        $target = IsuAdmin::getById($id);
        if (!$target) {
            return $this->back('error', 'Admin not found.');
        }

        IsuAdmin::setActive($id, true);
        IsuAudit::log('admin.activate', 'Reactivated ' . $target['email'], $this->me());
        return $this->back('success', $target['email'] . ' reactivated.');
    }

    /* ------------------------------------------------------------------ */

    private function generateTempPassword(): string
    {
        // Strong, readable, and satisfies the change-password policy
        // (upper + lower + digit, >= 10 chars).
        return 'Isu' . random_int(10, 99) . '-' . bin2hex(random_bytes(4)) . '!';
    }

    private function me(): array
    {
        return [
            'id'       => $this->isuUser['id'] ?? null,
            'username' => $this->isuUser['username'] ?? null,
            'email'    => $this->isuUser['email'] ?? null,
        ];
    }

    /** Require POST + valid CSRF; on failure flash and redirect. */
    private function guardPost(): bool
    {
        $isPost = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
        if (!$isPost || !$this->validCsrf()) {
            $this->flash('isu_users_flash', ['type' => 'error', 'message' => 'Invalid or expired request. Please try again.']);
            redirect('isu/users/index');
            return false;
        }
        return true;
    }

    /** Flash a message and return to the list. */
    private function back(string $type, string $message): void
    {
        $this->flash('isu_users_flash', ['type' => $type, 'message' => $message]);
        redirect('isu/users/index');
    }

    private function flash(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    private function takeFlash(string $key)
    {
        $v = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $v;
    }
}
