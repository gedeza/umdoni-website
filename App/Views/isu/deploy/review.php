<?php
/**
 * ISU deploy tool — review a staged patch before applying. Data via $context->data.
 */
global $context;
$data = $context->data;
$token    = $data['token']      ?? '';
$original = $data['original']   ?? null;
$entries  = $data['entries']    ?? [];
$allOk    = !empty($data['all_ok']);
$csrf     = $data['csrf_token'] ?? '';
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };

$overwrite = 0; $new = 0; $blocked = 0;
foreach ($entries as $en) {
    if (!$en['ok']) { $blocked++; }
    elseif ($en['action'] === 'overwrite') { $overwrite++; }
    else { $new++; }
}
?>

<div class="isu-card p-4">
    <h6 class="muted text-uppercase mb-2">Review patch<?php echo $original ? ': ' . $e($original) : ''; ?></h6>
    <p class="mb-3">
        <span class="status-live"><?php echo (int) $new; ?> new</span> ·
        <span class="text-warning"><?php echo (int) $overwrite; ?> overwrite</span> ·
        <span class="status-down"><?php echo (int) $blocked; ?> blocked</span>
    </p>

    <?php if (!$allOk): ?>
        <div class="alert alert-danger py-2">
            This patch contains <strong>blocked</strong> entries (below). For safety, it cannot be applied
            until every file is allowed. Fix the ZIP and re-upload.
        </div>
    <?php endif; ?>

    <div class="table-responsive mb-3">
        <table class="table table-sm align-middle mb-0">
            <thead><tr><th>File</th><th>Action</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($entries as $en): ?>
                    <tr>
                        <td style="font-size:.82rem; word-break:break-all;"><?php echo $e($en['name']); ?></td>
                        <td>
                            <?php if (!$en['ok']): ?>
                                <span class="muted">—</span>
                            <?php elseif ($en['action'] === 'overwrite'): ?>
                                <span class="text-warning">overwrite</span>
                            <?php else: ?>
                                <span class="status-live">new</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($en['ok']): ?>
                                <span class="status-live">✓ allowed</span>
                            <?php else: ?>
                                <span class="status-down">✗ <?php echo $e($en['reason']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex gap-2">
        <?php if ($allOk): ?>
            <form method="post" action="<?php echo buildurl('isu/deploy/apply'); ?>"
                  onsubmit="return confirm('Apply this patch to the live site? Overwritten files are backed up for rollback.');">
                <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                <input type="hidden" name="token" value="<?php echo $e($token); ?>">
                <button class="btn btn-warning">Apply patch</button>
            </form>
        <?php endif; ?>
        <form method="post" action="<?php echo buildurl('isu/deploy/discard'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
            <input type="hidden" name="token" value="<?php echo $e($token); ?>">
            <button class="btn btn-outline-light">Discard</button>
        </form>
    </div>
</div>
