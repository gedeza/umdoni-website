<?php
/**
 * @author : rakheoana lefela
 * @date : 16th dec 2021
 * 
 * Front Controller/ hadles all the incoming requests to site
 */
namespace App\Controllers;

use \Core\View;
use App\Models\TenderModel;

 

class Tenders extends \Core\Controller
{
  /**
     * Before filter
     *
     * @return void
     */
    protected function before()
    {
    }

    public function indexAction()
    {
        // Use GetActive() to show only non-expired tenders
        $tenders = TenderModel::GetActive();
        view::render('tenders/index.php', $tenders, 'default');
    }

    public function archiveAction()
    {
        // Show archived (expired) tenders
        $tenders = TenderModel::GetArchived();
        view::render('tenders/archive.php', $tenders, 'default');
    }

  
    /**
     * After filter
     *
     * @return void
     */
    protected function after()
    {
        //echo " (after)";
    }

}