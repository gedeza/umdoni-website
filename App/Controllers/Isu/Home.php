<?php

namespace App\Controllers\Isu;

use App\Models\SiteControl;
use App\Models\IsuAdmin;
use App\Models\IsuBackup;
use App\Models\IsuMigration;
use App\Models\IsuAudit;
use Core\View;

/**
 * Home — ISU console dashboard / landing page.
 *
 * At-a-glance status of everything the console manages, plus quick actions
 * and an onboarding panel, so a first-time user is oriented immediately.
 *
 * @author Nhlanhla Mnyandu <nhlanhla@isutech.co.za>
 */
class Home extends Guarded
{
    public function indexAction()
    {
        $backups = IsuBackup::all();
        $migrations = IsuMigration::status();
        $pending = 0;
        foreach ($migrations as $m) {
            if (empty($m['ran'])) { $pending++; }
        }
        $activeAdmins = 0;
        foreach (IsuAdmin::getAll() as $a) {
            if ((int) $a['active'] === 1) { $activeAdmins++; }
        }

        View::render('isu/home/index.php', [
            'page_title'    => 'Dashboard',
            'page_desc'     => 'Overview of everything the ISU console manages. Use the tabs above to act.',
            'suspended'     => SiteControl::isSuspended(),
            'suspend_info'  => SiteControl::suspensionInfo(),
            'active_admins' => $activeAdmins,
            'last_backup'   => !empty($backups) ? $backups[0] : null,
            'backup_count'  => count($backups),
            'pending_migrations' => $pending,
            'activity'      => IsuAudit::recent(8),
            'admin_name'    => $this->isuUser['username'] ?? '',
        ], 'isu');
    }
}
