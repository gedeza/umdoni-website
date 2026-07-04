<?php
/**
 * ISU console — sign in. Data via global $context->data (framework convention).
 */
global $context;
$data = $context->data;
$csrf  = $data['csrf_token'] ?? '';
$error = $data['error'] ?? null;
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2"><?php echo $e($error); ?></div>
<?php endif; ?>

<form method="post" action="<?php echo url('isu/auth/authenticate'); ?>" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?php echo $e($csrf); ?>">
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required autofocus>
    </div>
    <div class="mb-4">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-isu w-100">Sign in</button>
</form>

<p class="muted text-center mt-3 mb-0" style="font-size:.75rem;">Authorised ISU Technologies staff only.</p>
