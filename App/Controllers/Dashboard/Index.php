<?php

/**
 * @author : rakheoana lefela
 * @date : 16th dec 2021
 * 
 * Front Controller/ hadles all the incoming requests to site
 */

namespace App\Controllers\Dashboard;

use App\Repositories\Service;
use \Core\View;
use \Core\Session;

use App\Models\Profile;
use App\Models\Request;
use App\Models\ProjectsModel;
use App\Models\EventModel;
use App\Repositories\NoticeRepository;
use App\Models\UserModel;
use App\Models\LogsModel;


class Index extends \Core\Controller
{


    public function indexAction()
    {
        $dashboard = array();
        // get logged in user information
       
        
        $AuthenticatedUser =  (new Session)->getProfile();
        // check for user role
        $profile_id = $AuthenticatedUser['user_id'];
        if (count($AuthenticatedUser) > 0) {
            $profile = Profile::getUser($AuthenticatedUser['email']);
            foreach ($profile as $key => $value) {
                if ($value['user_id'] === $profile_id) {
                    $profile = $value;
                }
            }
        }
        $dashboard['users'] = UserModel::getUser($profile_id);
        $dashboard['requests'] = Request::getAll();
        $dashboard['projects'] = ProjectsModel::Get();
        $dashboard['events'] = EventModel::getAll();
        $dashboard['notices'] = NoticeRepository::getAll();
        $dashboard['requests'] = Request::getAll();
        $dashboard['profile'] = $profile;
        $profile['profile'] = $AuthenticatedUser;

        view::render('dashboard/index.php', $dashboard, 'dashboard');
    }

    /**
     * Session ping endpoint - keeps session alive
     * Called periodically by session-timeout.js
     */
    public function pingAction()
    {
        // Update session activity timestamp
        if (isset($_SESSION['profile'])) {
            $_SESSION['last_activity'] = time();

            // Return success response
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'timestamp' => time(),
                'session_active' => true
            ]);
            exit;
        }

        // Session not found
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode([
            'status' => 'error',
            'message' => 'Session expired'
        ]);
        exit;
    }

    /**
     * Log auto-logout event to Activity Logs
     * Called by session-timeout.js when user is auto-logged out
     */
    public function logAutoLogoutAction()
    {
        try {
            // Get reason and timestamp from POST data
            $reason = $_POST['reason'] ?? 'unknown';
            $timestamp = $_POST['timestamp'] ?? date('Y-m-d H:i:s');

            // Get user info from POST first (sent by JS before session expires), fallback to session
            $userId = $_POST['userId'] ?? $_SESSION['profile'][0]['user_id'] ?? 'unknown';
            $username = $_POST['username'] ?? $_SESSION['profile'][0]['username'] ?? 'Unknown User';
            $email = $_SESSION['profile'][0]['email'] ?? '';

            // Determine log message based on reason
            $message = match($reason) {
                'auto-logout' => 'Automatic logout due to inactivity (10 minutes)',
                'manual' => 'Manual logout from timeout warning',
                default => 'Session logout: ' . $reason
            };

            // Log the event
            LogsModel::LogError($message, 'info', [
                'userId' => $userId,
                'username' => $username,
                'email' => $email
            ]);

            // Return success (even if session is expired)
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'logged' => true
            ]);
            exit;
        } catch (\Exception $e) {
            // Log error but still return success to allow logout to proceed
            error_log('logAutoLogout Error: ' . $e->getMessage());

            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to log event, but logout will proceed'
            ]);
            exit;
        }
    }

    protected function before()
    {
        enable_authorize();
    }

    protected function after()
    {
    }
}
