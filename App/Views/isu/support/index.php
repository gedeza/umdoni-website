<?php
/**
 * ISU Support — ticket list + new ticket. Data via global $context->data.
 */
global $context;
$data = $context->data;
$tickets    = $data['tickets']    ?? [];
$counts     = $data['counts']     ?? [];
$filter     = $data['filter']     ?? '';
$statuses   = $data['statuses']   ?? [];
$priorities = $data['priorities'] ?? [];
$categories = $data['categories'] ?? [];
$csrf       = $data['csrf_token'] ?? '';
$flash      = $data['flash']      ?? null;
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$prioClass = ['low' => 'secondary', 'normal' => 'info', 'high' => 'warning', 'urgent' => 'danger'];
$statusClass = ['open' => 'success', 'in_progress' => 'info', 'on_hold' => 'secondary', 'resolved' => 'primary', 'closed' => 'dark'];
?>

<?php if (!empty($flash)): ?>
    <?php $cls = ['success' => 'success', 'error' => 'danger', 'info' => 'info'][$flash['type']] ?? 'secondary'; ?>
    <div class="alert alert-<?php echo $cls; ?>"><?php echo $e($flash['message']); ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- Ticket list -->
    <div class="col-lg-8">
        <div class="isu-card p-4">
            <!-- Filters -->
            <div class="d-flex flex-wrap gap-1 mb-3">
                <a class="btn btn-sm <?php echo $filter === '' ? 'btn-light' : 'btn-outline-light'; ?>"
                   href="<?php echo buildurl('isu/support/index'); ?>">All (<?php echo (int) ($counts['all'] ?? 0); ?>)</a>
                <?php foreach ($statuses as $key => $label): ?>
                    <a class="btn btn-sm <?php echo $filter === $key ? 'btn-light' : 'btn-outline-light'; ?>"
                       href="<?php echo buildurl('isu/support/index'); ?>?status=<?php echo urlencode($key); ?>">
                        <?php echo $e($label); ?> (<?php echo (int) ($counts[$key] ?? 0); ?>)
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (empty($tickets)): ?>
                <p class="muted mb-0">No tickets<?php echo $filter ? ' in this status' : ' yet'; ?>. Use the form to log one.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead><tr><th>Ref</th><th>Subject</th><th>Priority</th><th>Status</th><th>Updated</th></tr></thead>
                        <tbody>
                            <?php foreach ($tickets as $t): ?>
                                <tr>
                                    <td style="font-size:.82rem;"><a href="<?php echo buildurl('isu/support/view'); ?>?id=<?php echo (int) $t['id']; ?>"><?php echo $e($t['ref'] ?: ('#' . $t['id'])); ?></a></td>
                                    <td style="font-size:.85rem;">
                                        <a href="<?php echo buildurl('isu/support/view'); ?>?id=<?php echo (int) $t['id']; ?>" style="color:#e6edf3;"><?php echo $e($t['subject']); ?></a>
                                        <?php if (!empty($t['category'])): ?><br><span class="muted" style="font-size:.72rem;"><?php echo $e($t['category']); ?></span><?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-<?php echo $prioClass[$t['priority']] ?? 'secondary'; ?>"><?php echo $e($priorities[$t['priority']] ?? $t['priority']); ?></span></td>
                                    <td><span class="badge bg-<?php echo $statusClass[$t['status']] ?? 'secondary'; ?>"><?php echo $e($statuses[$t['status']] ?? $t['status']); ?></span></td>
                                    <td class="muted" style="font-size:.8rem;"><?php echo $e($t['updated_at'] ?: $t['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- New ticket -->
    <div class="col-lg-4">
        <div class="isu-card p-4 h-100">
            <h6 class="muted text-uppercase mb-3">Log a ticket</h6>
            <form method="post" action="<?php echo buildurl('isu/support/create'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                <div class="mb-2">
                    <label class="form-label muted">Subject</label>
                    <input type="text" name="subject" maxlength="200" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label muted">Description</label>
                    <textarea name="description" rows="3" class="form-control"></textarea>
                </div>
                <div class="row g-2">
                    <div class="col-6 mb-2">
                        <label class="form-label muted">Category</label>
                        <select name="category" class="form-select">
                            <?php foreach ($categories as $c): ?><option><?php echo $e($c); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 mb-2">
                        <label class="form-label muted">Priority</label>
                        <select name="priority" class="form-select">
                            <?php foreach ($priorities as $k => $label): ?>
                                <option value="<?php echo $e($k); ?>" <?php echo $k === 'normal' ? 'selected' : ''; ?>><?php echo $e($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label muted">Requester (uMdoni)</label>
                    <input type="text" name="requester_name" maxlength="150" class="form-control" placeholder="Name">
                </div>
                <div class="mb-3">
                    <label class="form-label muted">Contact</label>
                    <input type="text" name="requester_contact" maxlength="150" class="form-control" placeholder="Email or phone">
                </div>
                <button class="btn btn-success w-100">Create ticket</button>
            </form>
        </div>
    </div>
</div>
