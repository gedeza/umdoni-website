<?php
/**
 * ISU console — change password. Data via global $context->data.
 */
global $context;
$data = $context->data;
$csrf       = $data['csrf_token'] ?? '';
$error      = $data['error'] ?? null;
$mustChange = (int) ($data['must_change'] ?? 0);
$minLen     = (int) ($data['min_len'] ?? 10);
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
?>

<?php if ($mustChange === 1): ?>
    <div class="alert alert-warning py-2">Please set a new password before continuing.</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2"><?php echo $e($error); ?></div>
<?php endif; ?>

<form method="post" action="<?php echo url('isu/auth/changepassword'); ?>" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
    <div class="mb-3">
        <label class="form-label">Current password</label>
        <input type="password" name="current_password" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
        <label class="form-label">New password</label>
        <input type="password" name="new_password" class="form-control" required>
        <div class="muted mt-1" style="font-size:.75rem;">
            At least <?php echo $e($minLen); ?> characters, with upper &amp; lower case and a number.
        </div>
    </div>
    <div class="mb-4">
        <label class="form-label">Confirm new password</label>
        <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <button class="btn btn-isu w-100">Update password</button>
</form>
