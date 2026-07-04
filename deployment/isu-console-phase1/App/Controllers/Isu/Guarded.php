<?php

namespace App\Controllers\Isu;

use App\Models\IsuAdmin;

/**
 * Guarded — base controller for the ISU (provider) console.
 *
 * Uses the STANDALONE ISU authentication (not the municipal login). Every
 * console action runs through before(), which enforces:
 *   1. A valid ISU session ($_SESSION['isu_admin']).
 *   2. The admin still exists and is active (re-checked against the DB).
 *   3. Session not idle-timed-out.
 *   4. A pending forced password change is completed first.
 *
 * Returning false from before() stops the action (see \Core\Controller::__call).
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
abstract class Guarded extends \Core\Controller
{
    /** Idle session timeout (seconds). */
    const IDLE_TIMEOUT = 1800; // 30 minutes

    /** @var array The authenticated ISU admin (fresh DB row). */
    protected $isuUser = [];

    protected function before()
    {
        // 1. Must have an ISU session.
        $session = $_SESSION['isu_admin'] ?? null;
        if (empty($session) || empty($session['id'])) {
            redirect('isu/auth/login');
            return false;
        }

        // 2. Idle timeout.
        $last = $session['last_activity'] ?? 0;
        if (time() - $last > self::IDLE_TIMEOUT) {
            unset($_SESSION['isu_admin'], $_SESSION['isu_csrf']);
            redirect('isu/auth/login');
            return false;
        }

        // 3. Re-verify the admin is still active in the DB each request.
        $admin = IsuAdmin::getActiveById($session['id']);
        if (!$admin) {
            unset($_SESSION['isu_admin'], $_SESSION['isu_csrf']);
            redirect('isu/auth/login');
            return false;
        }

        // 4. Force a pending password change before anything else.
        if ((int) $admin['must_change_password'] === 1) {
            redirect('isu/auth/changepassword');
            return false;
        }

        // Refresh activity + expose the admin to actions.
        $_SESSION['isu_admin']['last_activity'] = time();
        $this->isuUser = $admin;

        // 5. Ensure a CSRF token exists for this console session.
        if (empty($_SESSION['isu_csrf'])) {
            $_SESSION['isu_csrf'] = bin2hex(random_bytes(32));
        }

        return true;
    }

    /**
     * Validate a submitted CSRF token against the session token.
     */
    protected function validCsrf(): bool
    {
        $sent = $_POST['csrf_token'] ?? '';
        return !empty($_SESSION['isu_csrf'])
            && is_string($sent)
            && hash_equals($_SESSION['isu_csrf'], $sent);
    }

    protected function csrfToken(): string
    {
        return $_SESSION['isu_csrf'] ?? '';
    }
}
