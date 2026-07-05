<?php
/**
 * ISU console dashboard. Data via global $context->data.
 */
global $context;
$data = $context->data;
$suspended  = $data['suspended']  ?? false;
$admins     = (int) ($data['active_admins'] ?? 0);
$lastBackup = $data['last_backup'] ?? null;
$backupCount = (int) ($data['backup_count'] ?? 0);
$pending    = (int) ($data['pending_migrations'] ?? 0);
$activity   = $data['activity'] ?? [];
$adminName  = $data['admin_name'] ?? '';
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
?>

<!-- Welcome / onboarding -->
<div class="isu-card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h5 class="mb-1">Welcome<?php echo $adminName ? ', ' . $e($adminName) : ''; ?> 👋</h5>
            <p class="muted mb-0" style="font-size:.9rem;">This is your control panel for the uMdoni website. New here?
                <a href="<?php echo buildurl('isu/help/index'); ?>">Open Help &amp; Guides</a> for a walkthrough of each section.</p>
        </div>
        <a class="btn btn-sm btn-outline-light" href="<?php echo buildurl('isu/help/index'); ?>">❔ Help &amp; Guides</a>
    </div>
</div>

<!-- Status cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="isu-card p-3 h-100">
            <div class="muted text-uppercase mb-2" style="font-size:.72rem;">Site status</div>
            <div class="d-flex align-items-center gap-2">
                <span class="status-dot <?php echo $suspended ? 'dot-down' : 'dot-live'; ?>"></span>
                <span class="h5 mb-0 <?php echo $suspended ? 'status-down' : 'status-live'; ?>">
                    <?php echo $suspended ? 'SUSPENDED' : 'LIVE'; ?>
                </span>
            </div>
            <a class="d-inline-block mt-2" style="font-size:.8rem;" href="<?php echo buildurl('isu/console/index'); ?>">Manage &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="isu-card p-3 h-100">
            <div class="muted text-uppercase mb-2" style="font-size:.72rem;">Active ISU admins</div>
            <div class="h5 mb-0"><?php echo $admins; ?></div>
            <a class="d-inline-block mt-2" style="font-size:.8rem;" href="<?php echo buildurl('isu/users/index'); ?>">Manage &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="isu-card p-3 h-100">
            <div class="muted text-uppercase mb-2" style="font-size:.72rem;">Last backup</div>
            <div class="mb-0" style="font-size:.95rem;">
                <?php echo $lastBackup ? $e($lastBackup['date']) : '<span class="status-down">none yet</span>'; ?>
            </div>
            <a class="d-inline-block mt-2" style="font-size:.8rem;" href="<?php echo buildurl('isu/database/index'); ?>"><?php echo $backupCount; ?> total &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="isu-card p-3 h-100">
            <div class="muted text-uppercase mb-2" style="font-size:.72rem;">Pending migrations</div>
            <div class="h5 mb-0 <?php echo $pending > 0 ? 'text-warning' : ''; ?>"><?php echo $pending; ?></div>
            <a class="d-inline-block mt-2" style="font-size:.8rem;" href="<?php echo buildurl('isu/database/index'); ?>">Database &rarr;</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Quick actions -->
    <div class="col-lg-5">
        <div class="isu-card p-4 h-100">
            <h6 class="muted text-uppercase mb-3">Quick actions</h6>
            <div class="d-grid gap-2">
                <a class="btn btn-outline-light text-start" href="<?php echo buildurl('isu/console/index'); ?>">⚡ Take the site offline / restore it</a>
                <a class="btn btn-outline-light text-start" href="<?php echo buildurl('isu/users/index'); ?>">👥 Add or manage ISU admins</a>
                <a class="btn btn-outline-light text-start" href="<?php echo buildurl('isu/database/index'); ?>">🗄 Back up the database</a>
                <a class="btn btn-outline-light text-start" href="<?php echo buildurl('isu/deploy/index'); ?>">⬆ Deploy a code patch</a>
            </div>
        </div>
    </div>

    <!-- Recent activity -->
    <div class="col-lg-7">
        <div class="isu-card p-4 h-100">
            <h6 class="muted text-uppercase mb-3">Recent activity</h6>
            <?php if (empty($activity)): ?>
                <p class="muted mb-0">No activity recorded yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <tbody>
                            <?php foreach ($activity as $row): ?>
                                <tr>
                                    <td class="muted" style="font-size:.8rem; white-space:nowrap;"><?php echo $e($row['created_at'] ?? ''); ?></td>
                                    <td><code style="color:#12b886; font-size:.78rem;"><?php echo $e($row['action'] ?? ''); ?></code></td>
                                    <td class="muted" style="font-size:.82rem;"><?php echo $e($row['detail'] ?? ''); ?></td>
                                    <td style="font-size:.82rem;"><?php echo $e($row['actor_name'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
