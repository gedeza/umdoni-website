<?php
/**
 * ISU admin management. Data via global $context->data.
 */
global $context;
$data = $context->data;
$admins   = $data['admins']     ?? [];
$audit    = $data['audit']      ?? [];
$csrf     = $data['csrf_token'] ?? '';
$meId     = (int) ($data['me_id'] ?? 0);
$flash    = $data['flash']      ?? null;
$newCred  = $data['new_cred']   ?? null;
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
?>

<?php if (!empty($flash)): ?>
    <?php $cls = ['success' => 'success', 'error' => 'danger', 'info' => 'info'][$flash['type']] ?? 'secondary'; ?>
    <div class="alert alert-<?php echo $cls; ?>"><?php echo $e($flash['message']); ?></div>
<?php endif; ?>

<?php if (!empty($newCred)): ?>
    <div class="isu-card p-4 mb-4" style="border-color:#12b886;">
        <h6 class="status-live text-uppercase mb-2">🔑 One-time password — <?php echo $e($newCred['label']); ?></h6>
        <p class="muted mb-2">Copy this now and share it securely with <strong><?php echo $e($newCred['email']); ?></strong>.
            It won't be shown again, and they'll be forced to change it on first login.</p>
        <div class="d-flex align-items-center gap-3">
            <code style="font-size:1.2rem; background:#0b1220; padding:8px 14px; border-radius:8px; color:#12b886;"><?php echo $e($newCred['password']); ?></code>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Admin list -->
    <div class="col-lg-8">
        <div class="isu-card p-4">
            <h6 class="muted text-uppercase mb-3">ISU Admins</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th><th>Email</th><th>Status</th><th>Last login</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $a): ?>
                            <?php $isMe = ((int) $a['id'] === $meId); $active = ((int) $a['active'] === 1); ?>
                            <tr>
                                <td>
                                    <?php echo $e($a['username']); ?>
                                    <?php if ($isMe): ?><span class="badge bg-secondary ms-1">You</span><?php endif; ?>
                                    <?php if ((int) $a['must_change_password'] === 1): ?>
                                        <span class="badge bg-warning text-dark ms-1">Temp pw</span>
                                    <?php endif; ?>
                                </td>
                                <td class="muted"><?php echo $e($a['email']); ?></td>
                                <td>
                                    <?php if ($active): ?>
                                        <span class="status-live">Active</span>
                                    <?php else: ?>
                                        <span class="status-down">Disabled</span>
                                    <?php endif; ?>
                                </td>
                                <td class="muted" style="font-size:.85rem;">
                                    <?php echo $a['last_login_at'] ? $e($a['last_login_at']) : '—'; ?>
                                    <?php if (!empty($a['last_login_ip'])): ?><br><span style="font-size:.75rem;"><?php echo $e($a['last_login_ip']); ?></span><?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($isMe): ?>
                                        <span class="muted" style="font-size:.8rem;">—</span>
                                    <?php else: ?>
                                        <div class="d-inline-flex gap-1">
                                            <form method="post" action="<?php echo buildurl('isu/users/reset'); ?>"
                                                  onsubmit="return confirm('Reset password for <?php echo $e($a['email']); ?>?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int) $a['id']; ?>">
                                                <button class="btn btn-sm btn-outline-light">Reset pw</button>
                                            </form>
                                            <?php if ($active): ?>
                                                <form method="post" action="<?php echo buildurl('isu/users/deactivate'); ?>"
                                                      onsubmit="return confirm('Deactivate <?php echo $e($a['email']); ?>? Their access is cut immediately.');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                                                    <input type="hidden" name="id" value="<?php echo (int) $a['id']; ?>">
                                                    <button class="btn btn-sm btn-outline-danger">Deactivate</button>
                                                </form>
                                            <?php else: ?>
                                                <form method="post" action="<?php echo buildurl('isu/users/activate'); ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                                                    <input type="hidden" name="id" value="<?php echo (int) $a['id']; ?>">
                                                    <button class="btn btn-sm btn-outline-success">Activate</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add admin -->
    <div class="col-lg-4">
        <div class="isu-card p-4 h-100">
            <h6 class="muted text-uppercase mb-3">Add ISU Admin</h6>
            <form method="post" action="<?php echo buildurl('isu/users/create'); ?>" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                <div class="mb-3">
                    <label class="form-label muted">Full name</label>
                    <input type="text" name="username" maxlength="100" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label muted">Email</label>
                    <input type="email" name="email" maxlength="150" class="form-control" required>
                </div>
                <button class="btn btn-success w-100">Create admin</button>
                <p class="muted mt-2 mb-0" style="font-size:.75rem;">A one-time password is generated and shown once. The new admin must change it on first login.</p>
            </form>
        </div>
    </div>
</div>

<!-- Audit -->
<div class="isu-card p-4 mt-4">
    <h6 class="muted text-uppercase mb-3">Recent admin activity</h6>
    <?php if (empty($audit)): ?>
        <p class="muted mb-0">No activity yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead><tr><th>When</th><th>Action</th><th>Detail</th><th>By</th><th>IP</th></tr></thead>
                <tbody>
                    <?php foreach ($audit as $row): ?>
                        <tr>
                            <td class="muted" style="font-size:.85rem;"><?php echo $e($row['created_at'] ?? ''); ?></td>
                            <td><code style="color:#12b886;"><?php echo $e($row['action'] ?? ''); ?></code></td>
                            <td class="muted"><?php echo $e($row['detail'] ?? ''); ?></td>
                            <td><?php echo $e($row['actor_name'] ?? '—'); ?></td>
                            <td class="muted"><?php echo $e($row['ip_address'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
