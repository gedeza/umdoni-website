<?php
/**
 * ISU Console — service control dashboard.
 * Vars: $suspended, $info, $history, $csrf_token, $isuUser, $flash
 */
$e = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
?>

<?php if (!empty($flash)): ?>
    <?php $cls = ['success' => 'success', 'error' => 'danger', 'info' => 'info'][$flash['type']] ?? 'secondary'; ?>
    <div class="alert alert-<?php echo $cls; ?>"><?php echo $e($flash['message']); ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- Status + actions -->
    <div class="col-lg-7">
        <div class="isu-card p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="status-dot <?php echo $suspended ? 'dot-down' : 'dot-live'; ?>"></span>
                <h4 class="mb-0">
                    Service is
                    <span class="<?php echo $suspended ? 'status-down' : 'status-live'; ?>">
                        <?php echo $suspended ? 'SUSPENDED' : 'LIVE'; ?>
                    </span>
                </h4>
            </div>

            <?php if ($suspended): ?>
                <p class="muted mb-1">
                    Suspended <?php echo $e($info['suspended_at'] ?? ''); ?>
                    <?php if (!empty($info['actor_name'])): ?>
                        by <?php echo $e($info['actor_name']); ?>
                    <?php endif; ?>
                </p>
                <?php if (!empty($info['reason'])): ?>
                    <p class="muted mb-3">Reason: <em><?php echo $e($info['reason']); ?></em></p>
                <?php endif; ?>
                <p class="mb-4">The public website and municipal dashboard are currently offline.
                    Visitors see a neutral “service unavailable” page.</p>

                <form method="post" action="<?php echo url('isu/console/restore'); ?>"
                      onsubmit="return confirm('Bring the site back online now?');">
                    <input type="hidden" name="csrf_token" value="<?php echo $e($csrf_token); ?>">
                    <div class="mb-3">
                        <label class="form-label muted">Internal note (optional)</label>
                        <input type="text" name="reason" maxlength="255" class="form-control"
                               placeholder="e.g. Invoice #1234 settled">
                    </div>
                    <button class="btn btn-success btn-lg">Restore service</button>
                </form>
            <?php else: ?>
                <p class="mb-4">Taking the site down will show all visitors and municipal
                    admins a neutral “service temporarily unavailable — contact uMdoni technical
                    staff” page. This console stays reachable so you can restore service.</p>

                <form method="post" action="<?php echo url('isu/console/suspend'); ?>"
                      onsubmit="return confirm('This will take the ENTIRE site offline. Continue?');">
                    <input type="hidden" name="csrf_token" value="<?php echo $e($csrf_token); ?>">
                    <div class="mb-3">
                        <label class="form-label muted">Internal reason (optional, not shown publicly)</label>
                        <input type="text" name="reason" maxlength="255" class="form-control"
                               placeholder="e.g. Invoice #1234 overdue 30 days">
                    </div>
                    <button class="btn btn-danger btn-lg">Suspend service</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Session info -->
    <div class="col-lg-5">
        <div class="isu-card p-4 h-100">
            <h6 class="muted text-uppercase mb-3">Signed in as</h6>
            <p class="mb-1"><strong><?php echo $e(trim(($isuUser['username'] ?? '') . ' ' . ($isuUser['surname'] ?? ''))); ?></strong></p>
            <p class="muted mb-0"><?php echo $e($isuUser['email'] ?? ''); ?></p>
        </div>
    </div>
</div>

<!-- Audit history -->
<div class="isu-card p-4 mt-4">
    <h6 class="muted text-uppercase mb-3">Recent actions</h6>
    <?php if (empty($history)): ?>
        <p class="muted mb-0">No actions recorded yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Action</th>
                        <th>By</th>
                        <th>Reason</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $row): ?>
                        <tr>
                            <td class="muted"><?php echo $e($row['created_at'] ?? ''); ?></td>
                            <td>
                                <?php if (($row['action'] ?? '') === 'suspend'): ?>
                                    <span class="status-down">Suspended</span>
                                <?php else: ?>
                                    <span class="status-live">Restored</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $e($row['actor_name'] ?? '—'); ?></td>
                            <td class="muted"><?php echo $e($row['reason'] ?? ''); ?></td>
                            <td class="muted"><?php echo $e($row['ip_address'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
