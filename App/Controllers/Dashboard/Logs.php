<?php
/**
 * @author : rakheoana lefela
 * @date : 16th dec 2021
 * 
 * Front Controller/ hadles all the incoming requests to site
 */
namespace App\Controllers\Dashboard;

use \Core\View;
use App\Models\LogsModel;


class Logs extends \Core\Controller
{

    public function indexAction()
    {
        // Get filter parameters from URL
        $filterType = $_GET['type'] ?? null;
        $filterUser = $_GET['user'] ?? null;
        $limit = $_GET['limit'] ?? 100;

        // Get logs with optional filtering
        if ($filterType && $filterType !== 'all') {
            $logs = LogsModel::GetByType($filterType);
        } else {
            $logs = LogsModel::Get();
        }

        // Filter by user if specified
        if ($filterUser && !empty($filterUser)) {
            $logs = array_filter($logs, function($log) use ($filterUser) {
                return stripos($log['username'], $filterUser) !== false ||
                       stripos($log['email'], $filterUser) !== false;
            });
        }

        // Limit results
        $logs = array_slice($logs, 0, $limit);

        // Pass filters to view for form persistence
        $context = [
            'logs' => $logs,
            'filterType' => $filterType ?? 'all',
            'filterUser' => $filterUser ?? '',
            'limit' => $limit
        ];

        view::render('dashboard/logs/index.php', $context, 'dashboard');
    }
  
    protected function before()
    {
       enable_authorize();
        //log in actibvity log()
    }

    protected function after()
    {
    }

}