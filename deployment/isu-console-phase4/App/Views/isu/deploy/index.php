<?php
/**
 * ISU deploy tool — upload a patch ZIP + recent patches. Data via $context->data.
 */
global $context;
$data = $context->data;
$patches = $data['patches']    ?? [];
$csrf    = $data['csrf_token'] ?? '';
$flash   = $data['flash']      ?? null;
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
?>

<?php if (!empty($flash)): ?>
    <?php $cls = ['success' => 'success', 'error' => 'danger', 'info' => 'info'][$flash['type']] ?? 'secondary'; ?>
    <div class="alert alert-<?php echo $cls; ?>"><?php echo $e($flash['message']); ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- Upload -->
    <div class="col-lg-5">
        <div class="isu-card p-4 h-100">
            <h6 class="muted text-uppercase mb-3">Deploy a Patch</h6>
            <p class="muted" style="font-size:.8rem;">
                Upload a ZIP laid out from the project root (e.g. <code>App/…</code>, <code>public/…</code>).
                You'll review the exact file list before anything is written. Files may only land under
                <code>App/</code>, <code>public/</code>, <code>Components/</code>, <code>migrations/</code>.
                Protected files (Config, .env, .htaccess, vendor) are refused.
            </p>
            <form method="post" action="<?php echo buildurl('isu/deploy/upload'); ?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                <div class="mb-3">
                    <input type="file" name="patch" accept=".zip" class="form-control" required>
                </div>
                <button class="btn btn-warning w-100">Upload &amp; review</button>
                <p class="muted mt-2 mb-0" style="font-size:.72rem;">Max 20 MB / 500 files.</p>
            </form>
        </div>
    </div>

    <!-- Recent patches -->
    <div class="col-lg-7">
        <div class="isu-card p-4 h-100">
            <h6 class="muted text-uppercase mb-3">Recent Patches</h6>
            <?php if (empty($patches)): ?>
                <p class="muted mb-0">No patches applied yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>When</th><th>Patch</th><th>Files</th><th>By</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                        <tbody>
                            <?php foreach ($patches as $p): ?>
                                <tr>
                                    <td class="muted" style="font-size:.82rem;"><?php echo $e($p['applied_at']); ?></td>
                                    <td style="font-size:.8rem; word-break:break-all;"><?php echo $e($p['original_name'] ?: $p['token']); ?></td>
                                    <td class="muted"><?php echo (int) $p['file_count']; ?></td>
                                    <td style="font-size:.82rem;"><?php echo $e($p['actor_name'] ?? '—'); ?></td>
                                    <td>
                                        <?php if ($p['status'] === 'rolled_back'): ?>
                                            <span class="muted">rolled back</span>
                                        <?php else: ?>
                                            <span class="status-live">applied</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($p['status'] === 'applied'): ?>
                                            <form method="post" action="<?php echo buildurl('isu/deploy/rollback'); ?>"
                                                  onsubmit="return confirm('Roll back this patch? Overwritten files are restored and new files removed.');">
                                                <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                                                <input type="hidden" name="id" value="<?php echo (int) $p['id']; ?>">
                                                <button class="btn btn-sm btn-outline-warning">Roll back</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="muted" style="font-size:.8rem;">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
