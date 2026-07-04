<?php
/**
 * ISU DB tools — backups + migrations. Data via global $context->data.
 */
global $context;
$data = $context->data;
$backups    = $data['backups']    ?? [];
$migrations = $data['migrations'] ?? [];
$csrf       = $data['csrf_token'] ?? '';
$flash      = $data['flash']      ?? null;
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
?>

<?php if (!empty($flash)): ?>
    <?php $cls = ['success' => 'success', 'error' => 'danger', 'info' => 'info'][$flash['type']] ?? 'secondary'; ?>
    <div class="alert alert-<?php echo $cls; ?>"><?php echo $e($flash['message']); ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- Backups -->
    <div class="col-lg-7">
        <div class="isu-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="muted text-uppercase mb-0">Database Backups</h6>
                <form method="post" action="<?php echo buildurl('isu/database/backup'); ?>"
                      onsubmit="return confirm('Create a new database backup now?');">
                    <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                    <button class="btn btn-sm btn-success">Back up now</button>
                </form>
            </div>

            <?php if (empty($backups)): ?>
                <p class="muted mb-0">No backups found yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>File</th><th>Size</th><th>Date</th><th class="text-end">Actions</th></tr></thead>
                        <tbody>
                            <?php foreach ($backups as $b): ?>
                                <tr>
                                    <td style="font-size:.8rem; word-break:break-all;"><?php echo $e($b['filename']); ?></td>
                                    <td class="muted"><?php echo $e($b['size_h']); ?></td>
                                    <td class="muted" style="font-size:.85rem;"><?php echo $e($b['date']); ?></td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <a class="btn btn-sm btn-outline-light"
                                               href="<?php echo buildurl('isu/database/download'); ?>?file=<?php echo urlencode($b['filename']); ?>">Download</a>
                                            <form method="post" action="<?php echo buildurl('isu/database/deletebackup'); ?>"
                                                  onsubmit="return confirm('Delete this backup?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                                                <input type="hidden" name="file" value="<?php echo $e($b['filename']); ?>">
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Migrations -->
    <div class="col-lg-5">
        <div class="isu-card p-4 h-100">
            <h6 class="muted text-uppercase mb-3">Migrations</h6>
            <p class="muted" style="font-size:.8rem;">Pre-approved schema changes shipped in <code>/migrations</code>. Each runs once. No free-form SQL.</p>

            <?php if (empty($migrations)): ?>
                <p class="muted mb-0">No migration files present.</p>
            <?php else: ?>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($migrations as $m): ?>
                        <li class="d-flex justify-content-between align-items-center py-2" style="border-top:1px solid #1f2b40;">
                            <span style="font-size:.8rem; word-break:break-all;">
                                <?php echo $e($m['filename']); ?>
                                <?php if ($m['ran']): ?>
                                    <br><span class="status-live" style="font-size:.72rem;">✓ ran <?php echo $e($m['ran_at']); ?><?php echo $m['ran_by'] ? ' by ' . $e($m['ran_by']) : ''; ?></span>
                                <?php endif; ?>
                            </span>
                            <?php if ($m['ran']): ?>
                                <span class="muted" style="font-size:.75rem;">done</span>
                            <?php else: ?>
                                <form method="post" action="<?php echo buildurl('isu/database/runmigration'); ?>"
                                      onsubmit="return confirm('Run migration <?php echo $e($m['filename']); ?>? This changes the database.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                                    <input type="hidden" name="file" value="<?php echo $e($m['filename']); ?>">
                                    <button class="btn btn-sm btn-warning">Run</button>
                                </form>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
