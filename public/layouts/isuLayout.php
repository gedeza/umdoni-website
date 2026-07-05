<?php
    include_once '../Components/Helpers.php';

    // Which ISU section is active? (from the routed path in QUERY_STRING)
    $isu_q     = strtok((string) ($_SERVER['QUERY_STRING'] ?? ''), '&');
    $isu_parts = explode('/', trim($isu_q, '/'));
    $isu_active = (isset($isu_parts[0]) && $isu_parts[0] === 'isu')
        ? strtolower($isu_parts[1] ?? '') : '';
    if ($isu_active === '') { $isu_active = 'console'; }

    // Signed-in admin + optional page header (passed by controllers via $context->data)
    global $context;
    $isu_admin_name = $_SESSION['isu_admin']['username'] ?? '';
    $isu_page_title = isset($context->data['page_title']) ? $context->data['page_title'] : '';
    $isu_page_desc  = isset($context->data['page_desc'])  ? $context->data['page_desc']  : '';

    $isu_nav = [
        'home'     => ['label' => 'Home',         'icon' => '⌂', 'route' => 'isu/home/index'],
        'support'  => ['label' => 'Support',       'icon' => '🎫', 'route' => 'isu/support/index'],
        'console'  => ['label' => 'Site Control', 'icon' => '⚡', 'route' => 'isu/console/index'],
        'users'    => ['label' => 'ISU Admins',   'icon' => '👥', 'route' => 'isu/users/index'],
        'database' => ['label' => 'Database',      'icon' => '🗄', 'route' => 'isu/database/index'],
        'deploy'   => ['label' => 'Deploy',        'icon' => '⬆', 'route' => 'isu/deploy/index'],
        'help'     => ['label' => 'Help',          'icon' => '❔', 'route' => 'isu/help/index'],
    ];
    $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <title>ISU Console | Umdoni Municipality</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --isu:#0b2545; --isu-accent:#12b886; }
        body { background:#0b1220; color:#e6edf3; font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif; }
        .isu-topbar { background:var(--isu); border-bottom:3px solid var(--isu-accent); }
        .isu-brand { font-weight:700; letter-spacing:.5px; }
        .isu-badge { font-size:.68rem; text-transform:uppercase; letter-spacing:1px; }
        .isu-nav-link {
            color:#c7d2e0; text-decoration:none; font-size:.9rem; padding:6px 12px; border-radius:8px;
            border-bottom:2px solid transparent; white-space:nowrap; display:inline-flex; align-items:center; gap:6px;
        }
        .isu-nav-link:hover { color:#fff; background:rgba(255,255,255,.06); }
        .isu-nav-link.active { color:#fff; background:rgba(18,182,134,.15); border-bottom-color:var(--isu-accent); font-weight:600; }
        .isu-nav-ico { font-size:1rem; line-height:1; }
        .isu-card { background:#111a2b; border:1px solid #1f2b40; border-radius:12px; }
        .status-live { color:#12b886; } .status-down { color:#ff6b6b; }
        .status-dot { display:inline-block; width:12px; height:12px; border-radius:50%; }
        .dot-live { background:#12b886; box-shadow:0 0 10px #12b886; }
        .dot-down { background:#ff6b6b; box-shadow:0 0 10px #ff6b6b; }
        .table { color:#cfd8e3; } .table > :not(caption) > * > * { border-color:#1f2b40; }
        .muted { color:#8a97a8; }
        a { color:var(--isu-accent); }
        .isu-page-title { font-weight:700; margin:0; }
        .isu-whoami { font-size:.8rem; color:#c7d2e0; }
    </style>
</head>
<body>
    <nav class="isu-topbar pt-3 pb-2">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <span class="isu-brand">ISU&nbsp;Technologies <span class="badge bg-success isu-badge align-middle">Provider Console</span></span>
                <span class="d-flex align-items-center gap-3">
                    <?php if ($isu_admin_name !== ''): ?>
                        <span class="isu-whoami">Signed in as <strong><?php echo $esc($isu_admin_name); ?></strong></span>
                    <?php endif; ?>
                    <a class="btn btn-sm btn-outline-light" href="<?php echo buildurl('isu/auth/logout'); ?>">Sign out</a>
                </span>
            </div>
            <div class="d-flex flex-wrap gap-1">
                <?php foreach ($isu_nav as $key => $item): ?>
                    <a class="isu-nav-link <?php echo $isu_active === $key ? 'active' : ''; ?>"
                       href="<?php echo buildurl($item['route']); ?>">
                        <span class="isu-nav-ico"><?php echo $item['icon']; ?></span><?php echo $esc($item['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <?php if ($isu_page_title !== ''): ?>
            <div class="mb-4">
                <h4 class="isu-page-title"><?php echo $esc($isu_page_title); ?></h4>
                <?php if ($isu_page_desc !== ''): ?>
                    <p class="muted mb-0" style="font-size:.9rem;"><?php echo $esc($isu_page_desc); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        {{content}}
    </div>
</body>
</html>
