<?php

namespace App\Controllers\Isu;

use App\Models\IsuAdmin;
use Core\View;

/**
 * Auth — standalone login for the ISU (provider) console.
 *
 * Independent of the municipal authentication system: its own session key
 * ($_SESSION['isu_admin']), own credentials table (isu_admins), and lives
 * under the /isu namespace which is exempt from the site kill-switch — so
 * ISU can log in and control the console even while the public site is down.
 *
 * URLs:
 *   /isu/auth/login           login form
 *   /isu/auth/authenticate    (POST) credential check
 *   /isu/auth/logout          end ISU session
 *   /isu/auth/changepassword  forced/voluntary password change
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class Auth extends \Core\Controller
{
    const MAX_FAILS   = 5;
    const LOCK_SECS   = 900;   // 15 minutes
    const MIN_PW_LEN  = 10;

    public function loginAction()
    {
        if (!empty($_SESSION['isu_admin'])) {
            redirect('isu/home/index');
            return;
        }
        $this->ensureCsrf();
        View::render('isu/auth/login.php', [
            'csrf_token' => $_SESSION['isu_csrf'],
            'error'      => $this->takeFlash('isu_login_error'),
        ], 'isuAuth');
    }

    public function authenticateAction()
    {
        if (!$this->isPost() || !$this->validCsrf()) {
            $this->flash('isu_login_error', 'Invalid or expired form. Please try again.');
            redirect('isu/auth/login');
            return;
        }

        // Brute-force lockout (per session).
        if ($this->isLockedOut()) {
            $this->flash('isu_login_error', 'Too many attempts. Please wait 15 minutes and try again.');
            redirect('isu/auth/login');
            return;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        $admin = $email !== '' ? IsuAdmin::getActiveByEmail($email) : null;

        if (!$admin || !IsuAdmin::passwordMatches($admin, $password)) {
            $this->registerFailure();
            // Deliberately generic — don't reveal which part was wrong.
            $this->flash('isu_login_error', 'Incorrect email or password.');
            redirect('isu/auth/login');
            return;
        }

        // Success.
        $this->clearFailures();
        session_regenerate_id(true);
        $_SESSION['isu_admin'] = [
            'id'                   => (int) $admin['id'],
            'username'             => $admin['username'],
            'email'                => $admin['email'],
            'must_change_password' => (int) $admin['must_change_password'],
            'last_activity'        => time(),
        ];
        // rotate CSRF token on privilege change
        $_SESSION['isu_csrf'] = bin2hex(random_bytes(32));

        IsuAdmin::recordLogin($admin['id'], $_SERVER['REMOTE_ADDR'] ?? null);

        if ((int) $admin['must_change_password'] === 1) {
            redirect('isu/auth/changepassword');
        } else {
            redirect('isu/home/index');
        }
    }

    public function logoutAction()
    {
        unset($_SESSION['isu_admin'], $_SESSION['isu_csrf']);
        redirect('isu/auth/login');
    }

    public function changepasswordAction()
    {
        // Must have an ISU session (but allowed even when must_change = 1).
        if (empty($_SESSION['isu_admin'])) {
            redirect('isu/auth/login');
            return;
        }
        $this->ensureCsrf();

        if ($this->isPost()) {
            if (!$this->validCsrf()) {
                $this->flash('isu_pw_error', 'Invalid or expired form. Please try again.');
                redirect('isu/auth/changepassword');
                return;
            }

            $admin   = IsuAdmin::getActiveById($_SESSION['isu_admin']['id']);
            $current = (string) ($_POST['current_password'] ?? '');
            $new     = (string) ($_POST['new_password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');

            if (!$admin || !IsuAdmin::passwordMatches($admin, $current)) {
                $this->flash('isu_pw_error', 'Your current password is incorrect.');
                redirect('isu/auth/changepassword');
                return;
            }
            $strength = $this->passwordProblem($new);
            if ($strength !== null) {
                $this->flash('isu_pw_error', $strength);
                redirect('isu/auth/changepassword');
                return;
            }
            if ($new !== $confirm) {
                $this->flash('isu_pw_error', 'New password and confirmation do not match.');
                redirect('isu/auth/changepassword');
                return;
            }
            if (IsuAdmin::passwordMatches($admin, $new)) {
                $this->flash('isu_pw_error', 'New password must be different from the current one.');
                redirect('isu/auth/changepassword');
                return;
            }

            IsuAdmin::setPassword($admin['id'], password_hash($new, PASSWORD_DEFAULT));
            $_SESSION['isu_admin']['must_change_password'] = 0;
            $_SESSION['isu_csrf'] = bin2hex(random_bytes(32));
            $this->flash('isu_pw_ok', 'Password updated.');
            redirect('isu/home/index');
            return;
        }

        View::render('isu/auth/changepassword.php', [
            'csrf_token'  => $_SESSION['isu_csrf'],
            'error'       => $this->takeFlash('isu_pw_error'),
            'must_change' => (int) ($_SESSION['isu_admin']['must_change_password'] ?? 0),
            'min_len'     => self::MIN_PW_LEN,
        ], 'isuAuth');
    }

    /* ------------------------------------------------------------------ */

    private function passwordProblem(string $pw): ?string
    {
        if (strlen($pw) < self::MIN_PW_LEN) {
            return 'Password must be at least ' . self::MIN_PW_LEN . ' characters.';
        }
        if (!preg_match('/[a-z]/', $pw) || !preg_match('/[A-Z]/', $pw) || !preg_match('/\d/', $pw)) {
            return 'Password must include upper- and lower-case letters and a number.';
        }
        return null;
    }

    private function isPost(): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    private function ensureCsrf(): void
    {
        if (empty($_SESSION['isu_csrf'])) {
            $_SESSION['isu_csrf'] = bin2hex(random_bytes(32));
        }
    }

    private function validCsrf(): bool
    {
        $sent = $_POST['csrf_token'] ?? '';
        return !empty($_SESSION['isu_csrf']) && is_string($sent)
            && hash_equals($_SESSION['isu_csrf'], $sent);
    }

    private function isLockedOut(): bool
    {
        $f = $_SESSION['isu_login_fail'] ?? null;
        return is_array($f) && ($f['count'] ?? 0) >= self::MAX_FAILS
            && (time() - ($f['last'] ?? 0)) < self::LOCK_SECS;
    }

    private function registerFailure(): void
    {
        $f = $_SESSION['isu_login_fail'] ?? ['count' => 0, 'last' => 0];
        // reset the window if the lock period has elapsed
        if (time() - ($f['last'] ?? 0) >= self::LOCK_SECS) {
            $f = ['count' => 0, 'last' => 0];
        }
        $f['count'] = ($f['count'] ?? 0) + 1;
        $f['last']  = time();
        $_SESSION['isu_login_fail'] = $f;
    }

    private function clearFailures(): void
    {
        unset($_SESSION['isu_login_fail']);
    }

    private function flash(string $key, string $msg): void
    {
        $_SESSION[$key] = $msg;
    }

    private function takeFlash(string $key): ?string
    {
        $v = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $v;
    }
}
