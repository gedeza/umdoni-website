<?php

namespace App\Controllers\Isu;

use App\Models\SiteControl;

/**
 * Guarded — base controller for the ISU (provider) console.
 *
 * Every action in the /isu namespace runs through before(), which enforces:
 *   1. The visitor is logged in.
 *   2. The account is flagged is_isu = 1 (verified against the DB, not just
 *      the session). Municipal admins — even super-admins — are turned away.
 *
 * Returning false from before() stops the action from executing
 * (see \Core\Controller::__call).
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
abstract class Guarded extends \Core\Controller
{
    /** @var array The authenticated ISU admin's profile row. */
    protected $isuUser = [];

    protected function before()
    {
        // 1. Must be authenticated.
        if (empty($_SESSION['profile'])) {
            redirect('authentication/login');
            return false;
        }

        // 2. Must be an ISU provider admin (authoritative DB check).
        $profile = $_SESSION['profile'];
        $userId  = $profile['user_id'] ?? null;

        if (!SiteControl::isIsuAdmin($userId)) {
            // Not a provider account: pretend the console does not exist.
            // Send municipal users back to their own dashboard.
            redirect('dashboard/index/index');
            return false;
        }

        $this->isuUser = $profile;

        // 3. Ensure a CSRF token exists for this console session.
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
