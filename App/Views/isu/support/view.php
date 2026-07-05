<?php
/**
 * ISU Support — ticket detail. Data via global $context->data.
 */
global $context;
$data = $context->data;
$t          = $data['ticket']     ?? [];
$replies    = $data['replies']    ?? [];
$statuses   = $data['statuses']   ?? [];
$priorities = $data['priorities'] ?? [];
$assignees  = $data['assignees']  ?? [];
$csrf       = $data['csrf_token'] ?? '';
$flash      = $data['flash']      ?? null;
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$statusClass = ['open' => 'success', 'in_progress' => 'info', 'on_hold' => 'secondary', 'resolved' => 'primary', 'closed' => 'dark'];
$prioClass = ['low' => 'secondary', 'normal' => 'info', 'high' => 'warning', 'urgent' => 'danger'];
?>

<p class="mb-3"><a href="<?php echo buildurl('isu/support/index'); ?>">&larr; Back to tickets</a></p>

<?php if (!empty($flash)): ?>
    <?php $cls = ['success' => 'success', 'error' => 'danger', 'info' => 'info'][$flash['type']] ?? 'secondary'; ?>
    <div class="alert alert-<?php echo $cls; ?>"><?php echo $e($flash['message']); ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- Conversation -->
    <div class="col-lg-8">
        <div class="isu-card p-4 mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <h5 class="mb-1"><?php echo $e($t['subject']); ?></h5>
                    <span class="muted" style="font-size:.82rem;">
                        <?php echo $e($t['ref'] ?: ('#' . $t['id'])); ?> ·
                        opened <?php echo $e($t['created_at']); ?>
                        <?php if (!empty($t['requester_name'])): ?> · by <?php echo $e($t['requester_name']); ?><?php endif; ?>
                        <?php if (!empty($t['requester_contact'])): ?> (<?php echo $e($t['requester_contact']); ?>)<?php endif; ?>
                    </span>
                </div>
                <span>
                    <span class="badge bg-<?php echo $prioClass[$t['priority']] ?? 'secondary'; ?>"><?php echo $e($priorities[$t['priority']] ?? ''); ?></span>
                    <span class="badge bg-<?php echo $statusClass[$t['status']] ?? 'secondary'; ?>"><?php echo $e($statuses[$t['status']] ?? ''); ?></span>
                </span>
            </div>
            <?php if (!empty($t['description'])): ?>
                <hr style="border-color:#1f2b40;">
                <p class="mb-0" style="white-space:pre-wrap;"><?php echo $e($t['description']); ?></p>
            <?php endif; ?>
        </div>

        <!-- Replies -->
        <?php foreach ($replies as $r): ?>
            <div class="isu-card p-3 mb-2">
                <div class="muted mb-1" style="font-size:.78rem;"><strong style="color:#c7d2e0;"><?php echo $e($r['author_name'] ?: 'ISU'); ?></strong> · <?php echo $e($r['created_at']); ?></div>
                <div style="white-space:pre-wrap;"><?php echo $e($r['body']); ?></div>
            </div>
        <?php endforeach; ?>

        <!-- Add reply -->
        <div class="isu-card p-4 mt-3">
            <h6 class="muted text-uppercase mb-2">Add update / reply</h6>
            <form method="post" action="<?php echo buildurl('isu/support/reply'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                <input type="hidden" name="id" value="<?php echo (int) $t['id']; ?>">
                <textarea name="body" rows="3" class="form-control mb-2" placeholder="What happened / what you did…" required></textarea>
                <button class="btn btn-success">Add reply</button>
            </form>
        </div>
    </div>

    <!-- Manage -->
    <div class="col-lg-4">
        <div class="isu-card p-4">
            <h6 class="muted text-uppercase mb-3">Manage</h6>
            <form method="post" action="<?php echo buildurl('isu/support/update'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
                <input type="hidden" name="id" value="<?php echo (int) $t['id']; ?>">
                <div class="mb-2">
                    <label class="form-label muted">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach ($statuses as $k => $label): ?>
                            <option value="<?php echo $e($k); ?>" <?php echo $t['status'] === $k ? 'selected' : ''; ?>><?php echo $e($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-2">
                    <label class="form-label muted">Priority</label>
                    <select name="priority" class="form-select">
                        <?php foreach ($priorities as $k => $label): ?>
                            <option value="<?php echo $e($k); ?>" <?php echo $t['priority'] === $k ? 'selected' : ''; ?>><?php echo $e($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label muted">Assigned to</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">Unassigned</option>
                        <?php foreach ($assignees as $name): ?>
                            <option value="<?php echo $e($name); ?>" <?php echo ($t['assigned_to'] ?? '') === $name ? 'selected' : ''; ?>><?php echo $e($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-primary w-100">Save changes</button>
            </form>
        </div>
    </div>
</div>
