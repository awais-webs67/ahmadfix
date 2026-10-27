<?php
/**
 * Ahmad Fix — Shared admin layout (header menu + styles)
 * Not a page: included by the admin pages.
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

function adminHead($title, $active = '') {
    $items = [
        'index'    => ['📋', 'Leads'],
        'emails'   => ['✉️', 'Emails'],
        'reviews'  => ['⭐', 'Reviews'],
        'settings' => ['⚙️', 'Settings'],
    ];
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo e($title); ?> — <?php echo e(SITE_NAME); ?> Admin</title>
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/images/favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700&display=swap">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', 'Tajawal', sans-serif; }
        body { background: #0b0f19; color: #f8fafc; min-height: 100vh; padding-bottom: 60px; }
        a { color: #ff9a33; }
        .a-header { background: #151d2a; border-bottom: 1px solid #233044; padding: 14px 24px; display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; position: sticky; top: 0; z-index: 50; }
        .a-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; color: #fff; }
        .a-brand img { height: 36px; background: #fff; border-radius: 6px; padding: 3px 6px; }
        .a-brand span { font-size: 17px; font-weight: 800; color: #ff7a00; }
        .a-nav { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
        .a-nav a { padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; color: #cbd5e1; border: 1px solid transparent; }
        .a-nav a:hover { background: #1e293b; }
        .a-nav a.active { background: #ff7a00; color: #fff; }
        .a-nav a.out { color: #f87171; border-color: rgba(239,68,68,.4); }
        .a-wrap { max-width: 1200px; margin: 26px auto; padding: 0 18px; }
        .a-title { font-size: 22px; font-weight: 800; margin-bottom: 6px; }
        .a-sub { color: #94a3b8; font-size: 14px; margin-bottom: 22px; line-height: 1.6; }
        .card { background: #151d2a; border: 1px solid #233044; border-radius: 14px; padding: 22px; margin-bottom: 20px; }
        .card h2 { font-size: 17px; font-weight: 800; margin-bottom: 4px; }
        .card .hint { color: #94a3b8; font-size: 13px; margin-bottom: 16px; line-height: 1.6; }
        .toast { padding: 13px 16px; border-radius: 10px; margin-bottom: 18px; font-weight: 600; font-size: 14px; background: rgba(16,185,129,.12); border: 1px solid #10b981; color: #6ee7b7; line-height: 1.5; }
        .toast.warning { background: rgba(245,158,11,.12); border-color: #f59e0b; color: #fcd34d; }
        .toast.error { background: rgba(239,68,68,.12); border-color: #ef4444; color: #fca5a5; }
        label { display: block; font-size: 12px; font-weight: 700; color: #94a3b8; margin-bottom: 6px; letter-spacing: .3px; }
        .help { font-size: 12px; color: #64748b; margin-top: 5px; line-height: 1.5; }
        input[type=text], input[type=email], input[type=url], input[type=number], input[type=password], input[type=time], input[type=date], textarea, select {
            width: 100%; padding: 10px 12px; background: #0b0f19; border: 1px solid #334155; border-radius: 8px; color: #fff; font-size: 14px; outline: none; }
        textarea { resize: vertical; line-height: 1.55; min-height: 80px; }
        input:focus, textarea:focus, select:focus { border-color: #ff7a00; }
        [dir=rtl] { text-align: right; }
        .fg { margin-bottom: 16px; }
        .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
        .grid3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0 16px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; cursor: pointer; border: 1px solid transparent; }
        .btn-orange { background: #ff7a00; color: #fff; }
        .btn-orange:hover { background: #e56d00; }
        .btn-blue { background: #1e293b; color: #38bdf8; border-color: #0284c7; }
        .btn-green { background: #065f46; color: #6ee7b7; border-color: #059669; }
        .btn-gray { background: #1e293b; color: #cbd5e1; border-color: #334155; }
        .btn-red { background: rgba(239,68,68,.15); color: #f87171; border-color: #ef4444; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .row-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .badge { display: inline-block; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; }
        .b-new { background: rgba(255,122,0,.15); color: #ff9a33; }
        .b-contacted { background: rgba(56,189,248,.15); color: #38bdf8; }
        .b-completed { background: rgba(16,185,129,.15); color: #34d399; }
        .b-cancelled { background: rgba(148,163,184,.15); color: #94a3b8; }
        .b-sent { background: rgba(16,185,129,.15); color: #34d399; }
        .b-failed { background: rgba(239,68,68,.15); color: #f87171; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: .6px; padding: 12px; border-bottom: 1px solid #233044; }
        td { padding: 12px; border-bottom: 1px solid #1c2636; font-size: 14px; vertical-align: top; }
        .table-wrap { overflow-x: auto; }
        .muted { color: #94a3b8; font-size: 13px; }
        .switch { display: flex; align-items: center; gap: 10px; font-size: 14px; color: #e2e8f0; font-weight: 500; }
        .switch input { width: 18px; height: 18px; accent-color: #ff7a00; }
        @media (max-width: 760px) {
            .a-header { padding: 12px 14px; }
            .a-nav { width: 100%; display: grid; grid-template-columns: repeat(3, 1fr); }
            .a-nav a { text-align: center; padding: 9px 6px; }
            .grid2, .grid3 { grid-template-columns: 1fr; }
            .a-wrap { padding: 0 12px; margin-top: 18px; }
            .card { padding: 16px; }
        }
    </style>
</head>
<body>
<header class="a-header">
    <a href="<?php echo SITE_URL; ?>/admin/index" class="a-brand">
        <img src="<?php echo ASSETS_URL; ?>/images/logo-header.png" alt="<?php echo e(SITE_NAME); ?>">
        <span>Admin</span>
    </a>
    <nav class="a-nav">
        <?php foreach ($items as $page => [$ic, $label]): ?>
        <a href="<?php echo SITE_URL; ?>/admin/<?php echo $page; ?>" class="<?php echo $active === $page ? 'active' : ''; ?>"><?php echo $ic . ' ' . $label; ?></a>
        <?php endforeach; ?>
        <a href="<?php echo SITE_URL; ?>/<?php echo 'en'; ?>" target="_blank" rel="noopener">🌐 View site</a>
        <a href="<?php echo SITE_URL; ?>/admin/logout" class="out">Sign out</a>
    </nav>
</header>
<main class="a-wrap">
    <?php
}

function adminFoot() {
    ?>
</main>
</body>
</html>
    <?php
}

/**
 * Show the one-time message saved before a redirect
 */
function adminFlash() {
    if (!empty($_SESSION['flash'])) {
        [$msg, $type] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="toast ' . e($type === 'success' ? '' : $type) . '">' . e($msg) . '</div>';
    }
}

function flash($msg, $type = 'success') {
    $_SESSION['flash'] = [$msg, $type];
}
