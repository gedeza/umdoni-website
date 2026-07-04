<?php
    include_once '../Components/Helpers.php';
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
        .isu-badge { font-size:.7rem; text-transform:uppercase; letter-spacing:1px; }
        .isu-card { background:#111a2b; border:1px solid #1f2b40; border-radius:12px; }
        .status-live { color:#12b886; }
        .status-down { color:#ff6b6b; }
        .status-dot { display:inline-block; width:12px; height:12px; border-radius:50%; }
        .dot-live { background:#12b886; box-shadow:0 0 10px #12b886; }
        .dot-down { background:#ff6b6b; box-shadow:0 0 10px #ff6b6b; }
        .table { color:#cfd8e3; }
        .table > :not(caption) > * > * { border-color:#1f2b40; }
        .muted { color:#8a97a8; }
        a { color:var(--isu-accent); }
    </style>
</head>
<body>
    <nav class="isu-topbar py-3 mb-4">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="isu-brand">ISU&nbsp;Technologies <span class="badge bg-success isu-badge align-middle">Provider Console</span></span>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-sm btn-outline-light" href="<?php echo buildurl('isu/console/index'); ?>">Site Control</a>
                <a class="btn btn-sm btn-outline-light" href="<?php echo buildurl('isu/users/index'); ?>">ISU Admins</a>
                <a class="btn btn-sm btn-outline-light" href="<?php echo buildurl('isu/database/index'); ?>">Database</a>
                <a class="btn btn-sm btn-outline-light" href="<?php echo buildurl('isu/deploy/index'); ?>">Deploy</a>
                <a class="btn btn-sm btn-outline-light ms-2" href="<?php echo buildurl('isu/auth/logout'); ?>">Sign out</a>
            </div>
        </div>
    </nav>
    <div class="container pb-5">
        {{content}}
    </div>
</body>
</html>
