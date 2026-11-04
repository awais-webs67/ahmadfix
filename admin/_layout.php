<?php
/**
 * Mr Fix KSA — Shared admin layout (sidebar, top bar, design system)
 * Not a page: included by the admin pages.
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

/**
 * Small stroke icons used across the admin panel
 */
function adminIcon($name, $size = 18) {
    $p = [
        'leads'    => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>',
        'emails'   => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
        'reviews'  => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
        'site'     => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>',
        'logout'   => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
        'menu'     => '<line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>',
        'phone'    => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'chat'     => '<path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>',
        'eye'      => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'trash'    => '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>',
        'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>',
        'search'   => '<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>',
        'inbox'    => '<polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/>',
        'bolt'     => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
        'tool'     => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'check'    => '<polyline points="20 6 9 17 4 12"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'pin'      => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
        'plus'     => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'back'     => '<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>',
    ];
    return '<svg class="ico" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($p[$name] ?? '') . '</svg>';
}

/**
 * Page start. $opts: ['bare' => true] for pages without the sidebar (login, forced password change)
 */
function adminHead($title, $active = '', array $opts = []) {
    global $pdo;
    $bare = !empty($opts['bare']);
    $items = [
        'index'    => ['leads', 'Leads'],
        'emails'   => ['emails', 'Emails'],
        'reviews'  => ['reviews', 'Reviews'],
        'settings' => ['settings', 'Settings'],
    ];
    $new_count = 0;
    if (!$bare) {
        try { $new_count = (int)$pdo->query("SELECT COUNT(*) FROM contact_submissions WHERE status = 'new'")->fetchColumn(); } catch (Throwable $e) {}
    }
    $name = $_SESSION['admin_name'] ?? 'Admin';
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo e($title); ?> — <?php echo e(SITE_NAME); ?> Admin</title>
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700&display=swap">
    <style>
        :root {
            --bg: #F4F6FA; --card: #FFFFFF; --line: #E5E9F0; --line-2: #EEF1F5;
            --ink: #0F172A; --ink-2: #334155; --muted: #64748B; --faint: #94A3B8;
            --brand: #FF7A00; --brand-2: #E86A00; --brand-soft: #FFF4EA;
            --navy: #0B1B33; --navy-2: #13294B;
            --green: #0E7A43; --green-soft: #E8F6EE; --blue: #2563EB; --blue-soft: #EAF1FF;
            --red: #DC2626; --red-soft: #FDECEC; --amber: #B45309; --amber-soft: #FEF3E2;
            --radius: 14px; --shadow: 0 1px 2px rgba(15,23,42,.04), 0 1px 3px rgba(15,23,42,.06);
            --side: 248px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { font-family: 'Inter', 'Tajawal', system-ui, sans-serif; background: var(--bg); color: var(--ink); -webkit-font-smoothing: antialiased; }
        a { color: var(--brand-2); }
        .ico { flex: none; }

        /* ---- Sidebar ---- */
        .side { position: fixed; inset: 0 auto 0 0; width: var(--side); background: var(--navy); color: #C8D2E1; display: flex; flex-direction: column; z-index: 40; }
        .side-brand { display: flex; align-items: center; gap: 10px; padding: 20px 20px 18px; text-decoration: none; }
        .side-brand img { height: 38px; background: #fff; border-radius: 8px; padding: 4px 7px; }
        .side-brand span { color: #fff; font-weight: 800; font-size: 15px; letter-spacing: -.01em; }
        .side-brand small { display: block; color: #8A9BB5; font-weight: 600; font-size: 11px; letter-spacing: .08em; text-transform: uppercase; }
        .side-nav { padding: 8px 12px; display: grid; gap: 2px; }
        .side-label { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #6B7E9C; padding: 16px 12px 8px; }
        .side-nav a { display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; color: #C8D2E1; text-decoration: none; font-weight: 600; font-size: 14px; }
        .side-nav a:hover { background: rgba(255,255,255,.06); color: #fff; }
        .side-nav a.active { background: rgba(255,122,0,.14); color: #fff; box-shadow: inset 3px 0 0 var(--brand); }
        .side-nav a.active .ico { color: var(--brand); }
        .side-nav .nav-count { margin-left: auto; background: var(--brand); color: #fff; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 999px; }
        .side-foot { margin-top: auto; padding: 14px 12px 18px; border-top: 1px solid rgba(255,255,255,.08); }
        .side-user { display: flex; align-items: center; gap: 10px; padding: 8px 10px 12px; }
        .avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, var(--brand), #FFB066); color: #fff; display: grid; place-items: center; font-weight: 800; font-size: 14px; }
        .side-user b { color: #fff; font-size: 13px; display: block; }
        .side-user small { color: #8A9BB5; font-size: 12px; }

        /* ---- Top bar + content ---- */
        .main { margin-left: var(--side); min-height: 100vh; }
        .topbar { position: sticky; top: 0; z-index: 30; height: 64px; display: flex; align-items: center; gap: 14px; padding: 0 28px; background: rgba(244,246,250,.9); backdrop-filter: blur(8px); border-bottom: 1px solid var(--line); }
        .topbar h1 { font-size: 18px; font-weight: 800; letter-spacing: -.01em; }
        .topbar .grow { flex: 1; }
        .menu-btn { display: none; background: none; border: 0; color: var(--ink); cursor: pointer; padding: 6px; border-radius: 8px; }
        .a-wrap { max-width: 1240px; margin: 0 auto; padding: 26px 28px 60px; }
        .a-title { font-size: 24px; font-weight: 800; letter-spacing: -.02em; margin-bottom: 4px; }
        .a-sub { color: var(--muted); font-size: 14px; margin-bottom: 22px; line-height: 1.6; }

        /* ---- Cards ---- */
        .card { background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); padding: 22px; margin-bottom: 18px; box-shadow: var(--shadow); }
        .card h2 { font-size: 16px; font-weight: 800; letter-spacing: -.01em; margin-bottom: 4px; }
        .card .hint { color: var(--muted); font-size: 13px; margin-bottom: 16px; line-height: 1.6; }
        .card-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 14px; flex-wrap: wrap; }

        /* ---- Messages ---- */
        .toast { padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; font-weight: 600; font-size: 14px; line-height: 1.5; background: var(--green-soft); border: 1px solid #BFE6CF; color: #0B5F35; }
        .toast.warning { background: var(--amber-soft); border-color: #F6D9A8; color: #8A4B06; }
        .toast.error { background: var(--red-soft); border-color: #F5C2C2; color: #9B1C1C; }
        .toast.info { background: var(--blue-soft); border-color: #C9DAFF; color: #1E3A8A; }
        .toast a { color: inherit; text-decoration: underline; }

        /* ---- Forms ---- */
        label { display: block; font-size: 12px; font-weight: 700; color: var(--ink-2); margin-bottom: 6px; }
        .help { font-size: 12px; color: var(--muted); margin-top: 6px; line-height: 1.5; }
        input[type=text], input[type=email], input[type=url], input[type=number], input[type=password], input[type=time], input[type=date], input[type=search], textarea, select {
            width: 100%; padding: 10px 12px; background: #fff; border: 1px solid #D7DDE6; border-radius: 10px; color: var(--ink); font-size: 14px; outline: none; font-family: inherit; transition: border-color .15s, box-shadow .15s; }
        input:disabled { background: #F6F8FB; color: var(--muted); }
        textarea { resize: vertical; line-height: 1.55; min-height: 84px; }
        input:focus, textarea:focus, select:focus { border-color: var(--brand); box-shadow: 0 0 0 3px rgba(255,122,0,.15); }
        [dir=rtl] { text-align: right; }
        .fg { margin-bottom: 16px; }
        .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
        .grid3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0 16px; }
        .switch { display: flex; align-items: center; gap: 10px; font-size: 14px; color: var(--ink-2); font-weight: 500; }
        .switch input { width: 18px; height: 18px; accent-color: var(--brand); }

        /* ---- Buttons ---- */
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; cursor: pointer; border: 1px solid transparent; font-family: inherit; line-height: 1.2; transition: background .15s, border-color .15s, box-shadow .15s; white-space: nowrap; }
        .btn:disabled { opacity: .5; cursor: not-allowed; }
        .btn-orange { background: var(--brand); color: #fff; box-shadow: 0 4px 12px rgba(255,122,0,.25); }
        .btn-orange:hover { background: var(--brand-2); }
        .btn-gray { background: #fff; color: var(--ink-2); border-color: #D7DDE6; }
        .btn-gray:hover { border-color: #B8C2D1; background: #FAFBFC; }
        .btn-blue { background: var(--blue-soft); color: var(--blue); border-color: #C9DAFF; }
        .btn-green { background: var(--green); color: #fff; }
        .btn-green:hover { background: #0B6436; }
        .btn-red { background: #fff; color: var(--red); border-color: #F5C2C2; }
        .btn-red:hover { background: var(--red-soft); }
        .btn-sm { padding: 6px 11px; font-size: 12px; border-radius: 8px; }
        .icon-btn { width: 34px; height: 34px; padding: 0; }
        .row-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

        /* ---- Badges ---- */
        .badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 999px; text-transform: uppercase; letter-spacing: .03em; white-space: nowrap; }
        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; opacity: .8; }
        .b-new { background: var(--brand-soft); color: var(--brand-2); }
        .b-contacted { background: var(--blue-soft); color: var(--blue); }
        .b-completed, .b-sent { background: var(--green-soft); color: var(--green); }
        .b-cancelled { background: #F1F5F9; color: var(--muted); }
        .b-failed { background: var(--red-soft); color: var(--red); }

        /* ---- Tables ---- */
        .table-wrap { overflow-x: auto; margin: 0 -22px; padding: 0 22px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; font-weight: 700; padding: 10px 12px; border-bottom: 1px solid var(--line); background: #FAFBFC; white-space: nowrap; }
        td { padding: 14px 12px; border-bottom: 1px solid var(--line-2); font-size: 14px; vertical-align: top; }
        tr:last-child td { border-bottom: 0; }
        tbody tr:hover td { background: #FBFCFE; }
        .muted { color: var(--muted); font-size: 13px; }
        .empty { text-align: center; padding: 48px 20px; color: var(--muted); }
        .empty .ico { color: var(--faint); margin-bottom: 8px; }

        /* ---- Mobile ---- */
        .side-backdrop { display: none; }
        @media (max-width: 960px) {
            .side { transform: translateX(-100%); transition: transform .2s ease; }
            body.nav-open .side { transform: none; box-shadow: 0 0 40px rgba(0,0,0,.35); }
            body.nav-open .side-backdrop { display: block; position: fixed; inset: 0; background: rgba(11,27,51,.45); z-index: 35; }
            .main { margin-left: 0; }
            .menu-btn { display: inline-flex; }
            .topbar { padding: 0 14px; }
            .a-wrap { padding: 18px 14px 50px; }
        }
        @media (max-width: 700px) {
            .grid2, .grid3 { grid-template-columns: 1fr; }
            .card { padding: 16px; }
            .table-wrap { margin: 0 -16px; padding: 0 16px; }
            .topbar .hide-sm { display: none; }
        }
        /* Bare pages (login / first password) */
        body.bare { display: grid; place-items: center; min-height: 100vh; padding: 20px; background: radial-gradient(800px 400px at 80% -10%, rgba(255,122,0,.12), transparent 60%), var(--bg); }
        .bare-card { width: 100%; max-width: 420px; }
        .bare-logo { display: block; text-align: center; margin-bottom: 18px; }
        .bare-logo img { height: 54px; }
    </style>
</head>
<body class="<?php echo $bare ? 'bare' : ''; ?>">
<?php if ($bare): ?>
<div class="bare-card">
    <a class="bare-logo" href="<?php echo SITE_URL; ?>/en" target="_blank" rel="noopener"><img src="<?php echo ASSETS_URL; ?>/images/logo-header.png" alt="<?php echo e(SITE_NAME); ?>"></a>
<?php else: ?>
<aside class="side" id="side">
    <a href="<?php echo SITE_URL; ?>/admin/index" class="side-brand">
        <img src="<?php echo ASSETS_URL; ?>/images/logo-header.png" alt="">
        <span><?php echo e(SITE_NAME); ?><small>Admin panel</small></span>
    </a>
    <nav class="side-nav">
        <div class="side-label">Manage</div>
        <?php foreach ($items as $page => [$ic, $label]): ?>
        <a href="<?php echo SITE_URL; ?>/admin/<?php echo $page; ?>" class="<?php echo $active === $page ? 'active' : ''; ?>">
            <?php echo adminIcon($ic); ?> <?php echo $label; ?>
            <?php if ($page === 'index' && $new_count): ?><span class="nav-count"><?php echo $new_count; ?></span><?php endif; ?>
        </a>
        <?php endforeach; ?>
        <div class="side-label">Website</div>
        <a href="<?php echo SITE_URL; ?>/en" target="_blank" rel="noopener"><?php echo adminIcon('site'); ?> View website</a>
    </nav>
    <div class="side-foot">
        <div class="side-user"><div class="avatar"><?php echo e(mb_strtoupper(mb_substr($name, 0, 1))); ?></div><div><b><?php echo e($name); ?></b><small>Administrator</small></div></div>
        <nav class="side-nav" style="padding:0;"><a href="<?php echo SITE_URL; ?>/admin/logout"><?php echo adminIcon('logout'); ?> Sign out</a></nav>
    </div>
</aside>
<div class="side-backdrop" onclick="document.body.classList.remove('nav-open')"></div>
<div class="main">
    <header class="topbar">
        <button class="menu-btn" type="button" aria-label="Menu" onclick="document.body.classList.toggle('nav-open')"><?php echo adminIcon('menu', 22); ?></button>
        <h1><?php echo e($title); ?></h1>
        <div class="grow"></div>
        <?php echo $opts['actions'] ?? ''; ?>
    </header>
    <main class="a-wrap">
<?php endif; ?>
    <?php
}

function adminFoot(array $opts = []) {
    if (!empty($opts['bare'])) {
        echo "</div>\n</body>\n</html>\n";
        return;
    }
    ?>
    </main>
</div>
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
