<?php
    include_once '../Components/Helpers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <title>ISU Console — Sign in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- PWA -->
    <link rel="manifest" href="<?php echo url('manifest.json'); ?>">
    <meta name="theme-color" content="#0b2545">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="ISU Console">
    <link rel="apple-touch-icon" href="<?php echo url('assets/pwa/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" href="<?php echo url('assets/pwa/favicon-32.png'); ?>">
    <style>
        :root { --isu:#0b2545; --isu-accent:#12b886; }
        body {
            min-height:100vh; margin:0; display:flex; align-items:center; justify-content:center;
            background:#0b1220; color:#e6edf3; padding:24px;
            font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
        }
        .isu-auth-card {
            width:100%; max-width:400px; background:#111a2b; border:1px solid #1f2b40;
            border-top:3px solid var(--isu-accent); border-radius:12px; padding:32px;
        }
        .isu-auth-brand { font-weight:700; letter-spacing:.5px; text-align:center; margin-bottom:4px; }
        .isu-auth-sub { text-align:center; color:#8a97a8; font-size:.85rem; margin-bottom:22px; }
        .form-control { background:#0b1220; border-color:#1f2b40; color:#e6edf3; }
        .form-control:focus { background:#0b1220; color:#e6edf3; border-color:var(--isu-accent); box-shadow:none; }
        .form-label { color:#8a97a8; font-size:.85rem; }
        .btn-isu { background:var(--isu-accent); color:#08131f; font-weight:600; }
        .btn-isu:hover { filter:brightness(1.05); color:#08131f; }
        .muted { color:#8a97a8; }
    </style>
</head>
<body>
    <div class="isu-auth-card">
        <div class="isu-auth-brand">ISU&nbsp;Technologies</div>
        <div class="isu-auth-sub">Provider Console</div>
        {{content}}
    </div>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('<?php echo buildurl('isu/sw'); ?>').catch(function () {});
        }
    </script>
</body>
</html>
