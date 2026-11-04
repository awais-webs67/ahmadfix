<?php
/**
 * Ahmad Fix — Enterprise Admin Control Panel & Business Intelligence Dashboard
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/_layout.php';

// ============================================
// CSV Lead Export Handler
// ============================================
if (isset($_GET['export']) && $_GET['export'] === 'csv' && empty($_SESSION['must_change_password'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=AhmadFix_Leads_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    // Output UTF-8 BOM for Excel Arabic support
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, ['Lead ID', 'Customer Name', 'Phone Number', 'Email', 'Service Requested', 'City', 'Address', 'Preferred Date', 'Preferred Time', 'Customer Message', 'Status', 'Notes', 'Submitted Date']);
    
    // Decode stored HTML entities and stop Excel from running cells that start with = + - @
    $csv = function ($v) {
        $v = html_entity_decode((string)$v, ENT_QUOTES, 'UTF-8');
        return preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v;
    };

    $export_stmt = $pdo->query("SELECT * FROM contact_submissions ORDER BY submitted_at DESC");
    while ($row = $export_stmt->fetch()) {
        $row = array_map($csv, $row);
        fputcsv($output, [
            $row['id'],
            $row['customer_name'],
            $row['phone'],
            $row['email'] ?: 'Not Provided',
            serviceLabel($row['service_requested'], 'en'),
            $row['city'] ?? '',
            $row['address'] ?? '',
            $row['preferred_date'] ?? '',
            $row['preferred_time'] ?? '',
            $row['message'],
            strtoupper($row['status'] ?? ($row['is_read'] ? 'read' : 'new')),
            $row['notes'] ?? '',
            $row['submitted_at']
        ]);
    }
    fclose($output);
    exit;
}

// ============================================
// Action Handlers (Status Updates, Settings, Deletion)
// ============================================
$msg = '';
$msg_type = 'success';

$allowed_statuses = ['new', 'contacted', 'completed', 'cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        $msg = 'Security check failed. Please try again.';
        $msg_type = 'warning';
    } elseif ($_POST['action_type'] === 'change_password') {
        $new_pass = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');
        if (strlen($new_pass) < 10) {
            $msg = 'Password must be at least 10 characters.';
            $msg_type = 'warning';
        } elseif ($new_pass !== $confirm) {
            $msg = 'The two passwords do not match.';
            $msg_type = 'warning';
        } elseif (in_array(strtolower($new_pass), ['ahmadfix2024', 'admin12345', 'password123', '1234567890'], true)) {
            $msg = 'Please choose a less common password.';
            $msg_type = 'warning';
        } else {
            $pdo->prepare("UPDATE admins SET password = :p WHERE id = :id")
                ->execute([':p' => password_hash($new_pass, PASSWORD_DEFAULT), ':id' => (int)($_SESSION['admin_id'] ?? 0)]);
            $_SESSION['must_change_password'] = false;
            session_regenerate_id(true);
            $_SESSION['flash'] = ['Password changed successfully.', 'success'];
            header('Location: ' . SITE_URL . '/admin/index');
            exit;
        }
    } elseif ($_POST['action_type'] === 'lead_action' && empty($_SESSION['must_change_password'])) {
        $action_id = (int)($_POST['id'] ?? 0);
        $act = $_POST['act'] ?? '';
        if ($act === 'status' && in_array($_POST['val'] ?? '', $allowed_statuses, true)) {
            $new_status = $_POST['val'];
            $is_read = ($new_status === 'new') ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE contact_submissions SET status = :st, is_read = :rd WHERE id = :id");
            $stmt->execute([':st' => $new_status, ':rd' => $is_read, ':id' => $action_id]);
            $_SESSION['flash'] = ['Lead #' . $action_id . ' status updated to "' . strtoupper($new_status) . '".', 'success'];
        } elseif ($act === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM contact_submissions WHERE id = :id");
            $stmt->execute([':id' => $action_id]);
            $_SESSION['flash'] = ['Lead #' . $action_id . ' deleted successfully.', 'warning'];
        }
        // Back to the same filtered list
        $back = SITE_URL . '/admin/index' . (!empty($_POST['back']) && $_POST['back'][0] === '?' ? $_POST['back'] : '');
        header('Location: ' . $back);
        exit;
    }
}

if (!empty($_SESSION['flash'])) {
    [$msg, $msg_type] = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

$must_change_password = !empty($_SESSION['must_change_password']);

// Search and Filter logic
$search = trim($_GET['q'] ?? '');
$filter_status = trim($_GET['status'] ?? 'all');
if (!in_array($filter_status, array_merge(['all', 'unread'], $allowed_statuses), true)) {
    $filter_status = 'all';
}

$where_clauses = [];
$params = [];

if ($search !== '') {
    // Each placeholder used once (the database refuses repeated names)
    $where_clauses[] = "(customer_name LIKE :q1 OR phone LIKE :q2 OR email LIKE :q3 OR service_requested LIKE :q4 OR message LIKE :q5 OR city LIKE :q6)";
    foreach (range(1, 6) as $n) $params[":q$n"] = '%' . $search . '%';
}

if ($filter_status !== 'all') {
    if ($filter_status === 'unread') {
        $where_clauses[] = "is_read = 0";
    } else {
        $where_clauses[] = "status = :st_filter";
        $params[':st_filter'] = $filter_status;
    }
}

$where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Stats
$counts = ['all' => 0, 'new' => 0, 'contacted' => 0, 'completed' => 0, 'cancelled' => 0];
foreach ($pdo->query("SELECT status, COUNT(*) n FROM contact_submissions GROUP BY status") as $r) {
    $counts[$r['status']] = (int)$r['n'];
    $counts['all'] += (int)$r['n'];
}
$today = (int)$pdo->query("SELECT COUNT(*) FROM contact_submissions WHERE DATE(submitted_at) = CURDATE()")->fetchColumn();
$week = (int)$pdo->query("SELECT COUNT(*) FROM contact_submissions WHERE submitted_at >= CURDATE() - INTERVAL 6 DAY")->fetchColumn();

// Leads per day, last 14 days (for the small chart)
$per_day = [];
foreach ($pdo->query("SELECT DATE(submitted_at) d, COUNT(*) n FROM contact_submissions WHERE submitted_at >= CURDATE() - INTERVAL 13 DAY GROUP BY DATE(submitted_at)") as $r) {
    $per_day[$r['d']] = (int)$r['n'];
}
$chart = [];
for ($k = 13; $k >= 0; $k--) {
    $d = date('Y-m-d', strtotime("-$k day"));
    $chart[] = ['d' => $d, 'n' => $per_day[$d] ?? 0];
}
$chart_max = max(1, max(array_column($chart, 'n')));

// Most requested service and city
$top_service = $pdo->query("SELECT service_requested s, COUNT(*) n FROM contact_submissions WHERE service_requested <> '' GROUP BY service_requested ORDER BY n DESC LIMIT 1")->fetch();
$top_city = $pdo->query("SELECT city c, COUNT(*) n FROM contact_submissions WHERE city <> '' GROUP BY city ORDER BY n DESC LIMIT 1")->fetch();

// Fetch filtered list (50 per page)
$per_page = 50;
$page_no = max(1, (int)($_GET['p'] ?? 1));
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_submissions $where_sql");
$count_stmt->execute($params);
$filtered_total = (int)$count_stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT * FROM contact_submissions $where_sql ORDER BY submitted_at DESC LIMIT $per_page OFFSET " . (($page_no - 1) * $per_page));
$stmt->execute($params);
$submissions = $stmt->fetchAll();

// Keep the current search/filter when a lead is updated
$back_query = '?' . http_build_query(array_filter([
    'q' => $search,
    'status' => $filter_status !== 'all' ? $filter_status : null,
    'p' => $page_no > 1 ? $page_no : null,
]));
$qs = fn(array $extra) => '?' . http_build_query(array_filter(array_merge(['q' => $search, 'status' => $filter_status !== 'all' ? $filter_status : null], $extra), fn($v) => $v !== null && $v !== ''));

$status_labels = ['new' => 'New', 'contacted' => 'In progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];

// ---------------- First login: choose a new password ----------------
if ($must_change_password) {
    adminHead('Set a new password', '', ['bare' => true]);
    ?>
    <div class="card" style="padding: 28px;">
        <h2 style="font-size: 20px;">🔒 Set a new admin password</h2>
        <p class="hint">You signed in with the default password. Choose your own now — the default stops working after this.</p>
        <?php if ($msg): ?><div class="toast error"><?php echo e($msg); ?></div><?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
            <input type="hidden" name="action_type" value="change_password">
            <div class="fg"><label>New password (min. 10 characters)</label><input type="password" name="new_password" autocomplete="new-password" minlength="10" required autofocus></div>
            <div class="fg"><label>Confirm new password</label><input type="password" name="confirm_password" autocomplete="new-password" minlength="10" required></div>
            <button type="submit" class="btn btn-orange" style="width: 100%; padding: 12px;">Save new password</button>
        </form>
        <p class="muted" style="margin-top: 14px; text-align: center;"><a href="<?php echo SITE_URL; ?>/admin/logout">Sign out</a></p>
    </div>
    <?php
    adminFoot(['bare' => true]);
    return;
}

// ---------------- Dashboard ----------------
adminHead('Leads', 'index', ['actions' =>
    '<a href="?export=csv" class="btn btn-gray hide-sm">' . adminIcon('download', 16) . ' Export CSV</a>']);
?>
<?php if ($msg): ?>
    <div class="toast <?php echo $msg_type === 'warning' ? 'warning' : ''; ?>"><?php echo e($msg); ?></div>
<?php endif; ?>

<div class="stats">
    <a class="stat" href="<?php echo e($qs(['status' => null, 'p' => null])); ?>">
        <span class="stat-ic" style="background:#EEF2F7;color:#334155;"><?php echo adminIcon('leads', 20); ?></span>
        <span class="stat-n"><?php echo $counts['all']; ?></span><span class="stat-l">All leads</span>
        <span class="stat-f"><?php echo $week; ?> in the last 7 days</span>
    </a>
    <a class="stat" href="<?php echo e($qs(['status' => 'new', 'p' => null])); ?>">
        <span class="stat-ic" style="background:var(--brand-soft);color:var(--brand-2);"><?php echo adminIcon('bolt', 20); ?></span>
        <span class="stat-n"><?php echo $counts['new']; ?></span><span class="stat-l">New — call back</span>
        <span class="stat-f"><?php echo $today; ?> today</span>
    </a>
    <a class="stat" href="<?php echo e($qs(['status' => 'contacted', 'p' => null])); ?>">
        <span class="stat-ic" style="background:var(--blue-soft);color:var(--blue);"><?php echo adminIcon('tool', 20); ?></span>
        <span class="stat-n"><?php echo $counts['contacted']; ?></span><span class="stat-l">In progress</span>
        <span class="stat-f">Technician sent / contacted</span>
    </a>
    <a class="stat" href="<?php echo e($qs(['status' => 'completed', 'p' => null])); ?>">
        <span class="stat-ic" style="background:var(--green-soft);color:var(--green);"><?php echo adminIcon('check', 20); ?></span>
        <span class="stat-n"><?php echo $counts['completed']; ?></span><span class="stat-l">Completed</span>
        <span class="stat-f"><?php echo $counts['all'] ? round($counts['completed'] / $counts['all'] * 100) : 0; ?>% of all leads</span>
    </a>
</div>

<div class="dash-row">
    <div class="card chart-card">
        <div class="card-head"><div><h2>Leads — last 14 days</h2><span class="muted"><?php echo array_sum(array_column($chart, 'n')); ?> bookings and messages</span></div></div>
        <div class="chart">
            <?php foreach ($chart as $c): $h = $c['n'] ? max(6, round($c['n'] / $chart_max * 100)) : 2; ?>
            <div class="bar-col" title="<?php echo e(date('D j M', strtotime($c['d'])) . ': ' . $c['n']); ?>">
                <span class="bar-n"><?php echo $c['n'] ?: ''; ?></span>
                <span class="bar <?php echo $c['d'] === date('Y-m-d') ? 'today' : ''; ?>" style="height: <?php echo $h; ?>%"></span>
                <span class="bar-d"><?php echo e(date('j', strtotime($c['d']))); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card insight">
        <h2>At a glance</h2>
        <div class="ins-row"><span class="ins-ic"><?php echo adminIcon('tool', 18); ?></span><div><span class="muted">Most requested</span><b><?php echo $top_service ? e(serviceLabel($top_service['s'], 'en')) : '—'; ?></b></div></div>
        <div class="ins-row"><span class="ins-ic"><?php echo adminIcon('pin', 18); ?></span><div><span class="muted">Top city</span><b><?php echo $top_city ? e($top_city['c']) : '—'; ?></b></div></div>
        <div class="ins-row"><span class="ins-ic"><?php echo adminIcon('calendar', 18); ?></span><div><span class="muted">Today</span><b><?php echo $today; ?> new lead<?php echo $today === 1 ? '' : 's'; ?></b></div></div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <div class="tabs">
            <?php foreach (['all' => 'All', 'new' => 'New', 'contacted' => 'In progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $k => $l): ?>
            <a href="<?php echo e($qs(['status' => $k === 'all' ? null : $k, 'p' => null])); ?>" class="tab <?php echo $filter_status === $k ? 'on' : ''; ?>"><?php echo $l; ?> <span><?php echo $counts[$k] ?? 0; ?></span></a>
            <?php endforeach; ?>
        </div>
        <form method="GET" action="" class="search">
            <?php echo adminIcon('search', 16); ?>
            <input type="search" name="q" value="<?php echo e($search); ?>" placeholder="Search name, phone, city…">
            <?php if ($filter_status !== 'all'): ?><input type="hidden" name="status" value="<?php echo e($filter_status); ?>"><?php endif; ?>
        </form>
    </div>

    <?php if (empty($submissions)): ?>
        <div class="empty"><?php echo adminIcon('inbox', 34); ?><div><b>No leads here yet</b></div><div class="muted">New bookings from the website appear here instantly.</div></div>
    <?php else: ?>
    <div class="table-wrap">
    <table class="leads">
        <thead><tr><th>Customer</th><th>Service</th><th>When</th><th>Status</th><th style="text-align:right;">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($submissions as $sub):
            $cust_name = e($sub['customer_name']);
            $svc_name = serviceLabel(strtolower($sub['service_requested']), 'en');
            $curr_status = $sub['status'] ?? 'new';
            $wa_msg = rawurlencode("Hello " . html_entity_decode($cust_name, ENT_QUOTES, 'UTF-8') . ", thank you for contacting " . SITE_NAME . "! We received your request for {$svc_name}.");
            $wa_url = "https://wa.me/" . customerWhatsApp($sub['phone']) . "?text=" . $wa_msg;
            $tel = preg_replace('/[^+0-9]/', '', $sub['phone']);
            $lead_url = SITE_URL . '/admin/lead?id=' . (int)$sub['id'];
        ?>
            <tr class="<?php echo $curr_status === 'new' ? 'is-new' : ''; ?>">
                <td>
                    <a href="<?php echo $lead_url; ?>" class="cust"><span class="avatar sm"><?php echo e(mb_strtoupper(mb_substr(html_entity_decode($sub['customer_name'], ENT_QUOTES, 'UTF-8'), 0, 1))); ?></span>
                        <span><b><?php echo $cust_name; ?></b><span class="muted" dir="ltr"><?php echo e($sub['phone']); ?></span></span></a>
                </td>
                <td>
                    <b class="svc"><?php echo e($svc_name); ?></b>
                    <div class="muted"><?php echo !empty($sub['city']) ? e($sub['city']) : '—'; ?><?php if (!empty($sub['preferred_date'])): ?> · 🗓️ <?php echo e(date('D j M', strtotime($sub['preferred_date']))); ?><?php echo !empty($sub['preferred_time']) ? ' ' . e(ucfirst($sub['preferred_time'])) : ''; ?><?php endif; ?></div>
                    <?php if (!empty($sub['message'])): ?><div class="msg"><?php echo e(mb_strimwidth(html_entity_decode($sub['message'], ENT_QUOTES, 'UTF-8'), 0, 90, '…')); ?></div><?php endif; ?>
                </td>
                <td class="muted" style="white-space:nowrap;"><?php echo date('j M Y', strtotime($sub['submitted_at'])); ?><br><?php echo date('g:i A', strtotime($sub['submitted_at'])); ?></td>
                <td>
                    <form method="POST" action="" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
                        <input type="hidden" name="action_type" value="lead_action">
                        <input type="hidden" name="act" value="status">
                        <input type="hidden" name="id" value="<?php echo (int)$sub['id']; ?>">
                        <input type="hidden" name="back" value="<?php echo e($back_query); ?>">
                        <select name="val" onchange="this.form.submit()" aria-label="Lead status" class="status-select st-<?php echo e($curr_status); ?>">
                            <?php foreach ($status_labels as $sv => $sl): ?><option value="<?php echo $sv; ?>" <?php echo $curr_status === $sv ? 'selected' : ''; ?>><?php echo $sl; ?></option><?php endforeach; ?>
                        </select>
                    </form>
                </td>
                <td>
                    <div class="row-actions" style="justify-content:flex-end;">
                        <a href="tel:<?php echo e($tel); ?>" class="btn btn-gray btn-sm icon-btn" title="Call"><?php echo adminIcon('phone', 15); ?></a>
                        <a href="<?php echo e($wa_url); ?>" target="_blank" rel="noopener" class="btn btn-gray btn-sm icon-btn" title="WhatsApp" style="color:var(--green);"><?php echo adminIcon('chat', 15); ?></a>
                        <a href="<?php echo $lead_url; ?>" class="btn btn-orange btn-sm" title="Open, reply by email"><?php echo adminIcon('eye', 15); ?> Open</a>
                        <form method="POST" action="" class="inline-form" onsubmit="return confirm('Delete lead #<?php echo (int)$sub['id']; ?>?');">
                            <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
                            <input type="hidden" name="action_type" value="lead_action">
                            <input type="hidden" name="act" value="delete">
                            <input type="hidden" name="id" value="<?php echo (int)$sub['id']; ?>">
                            <input type="hidden" name="back" value="<?php echo e($back_query); ?>">
                            <button type="submit" class="btn btn-red btn-sm icon-btn" title="Delete"><?php echo adminIcon('trash', 15); ?></button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php if ($filtered_total > $per_page): ?>
    <div class="pager">
        <span class="muted">Showing <?php echo ($page_no - 1) * $per_page + 1; ?>–<?php echo min($filtered_total, $page_no * $per_page); ?> of <?php echo $filtered_total; ?></span>
        <div class="row-actions">
            <?php if ($page_no > 1): ?><a class="btn btn-gray btn-sm" href="<?php echo e($qs(['p' => $page_no - 1])); ?>">← Newer</a><?php endif; ?>
            <?php if ($page_no * $per_page < $filtered_total): ?><a class="btn btn-gray btn-sm" href="<?php echo e($qs(['p' => $page_no + 1])); ?>">Older →</a><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<style>
    .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 18px; }
    .stat { background: #fff; border: 1px solid var(--line); border-radius: var(--radius); padding: 18px; box-shadow: var(--shadow); text-decoration: none; color: var(--ink); display: grid; grid-template-columns: auto 1fr; grid-template-rows: auto auto auto; column-gap: 14px; transition: border-color .15s, transform .15s; }
    .stat:hover { border-color: #FFC999; transform: translateY(-1px); }
    .stat-ic { grid-row: 1 / 3; width: 44px; height: 44px; border-radius: 12px; display: grid; place-items: center; }
    .stat-n { font-size: 26px; font-weight: 800; letter-spacing: -.02em; line-height: 1.1; }
    .stat-l { font-size: 13px; color: var(--muted); font-weight: 600; }
    .stat-f { grid-column: 1 / -1; margin-top: 12px; padding-top: 10px; border-top: 1px solid var(--line-2); font-size: 12px; color: var(--muted); }
    .dash-row { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; }
    .chart { display: grid; grid-template-columns: repeat(14, 1fr); gap: 8px; height: 150px; align-items: end; padding-top: 10px; }
    .bar-col { display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; gap: 4px; }
    .bar { width: 100%; max-width: 26px; background: #FFD2A8; border-radius: 6px 6px 3px 3px; }
    .bar.today { background: var(--brand); }
    .bar-n { font-size: 11px; font-weight: 700; color: var(--ink-2); min-height: 14px; }
    .bar-d { font-size: 11px; color: var(--faint); }
    .insight h2 { margin-bottom: 12px; }
    .ins-row { display: flex; gap: 12px; align-items: center; padding: 11px 0; border-top: 1px solid var(--line-2); }
    .ins-row:first-of-type { border-top: 0; }
    .ins-row b { display: block; font-size: 14px; }
    .ins-row .muted { font-size: 12px; }
    .ins-ic { width: 36px; height: 36px; border-radius: 10px; background: var(--brand-soft); color: var(--brand-2); display: grid; place-items: center; }
    .tabs { display: flex; gap: 4px; background: #F1F4F8; padding: 4px; border-radius: 11px; flex-wrap: wrap; }
    .tab { padding: 7px 12px; border-radius: 8px; font-size: 13px; font-weight: 700; color: var(--muted); text-decoration: none; }
    .tab span { font-size: 11px; background: #E2E8F0; color: var(--ink-2); border-radius: 999px; padding: 1px 7px; margin-left: 4px; }
    .tab.on { background: #fff; color: var(--ink); box-shadow: var(--shadow); }
    .tab.on span { background: var(--brand-soft); color: var(--brand-2); }
    .search { position: relative; min-width: 260px; }
    .search .ico { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--faint); }
    .search input { padding-left: 36px; }
    .cust { display: flex; gap: 12px; align-items: center; text-decoration: none; color: var(--ink); }
    .cust b { display: block; font-size: 14px; }
    .avatar.sm { width: 36px; height: 36px; font-size: 14px; flex: none; }
    .leads tr.is-new td:first-child { box-shadow: inset 3px 0 0 var(--brand); }
    .svc { font-size: 14px; }
    .msg { font-size: 13px; color: var(--ink-2); margin-top: 6px; background: #F7F9FC; border-radius: 8px; padding: 6px 9px; max-width: 360px; }
    .inline-form { display: inline; margin: 0; }
    .status-select { width: auto; padding: 6px 30px 6px 10px; font-size: 12px; font-weight: 700; border-radius: 999px; }
    .st-new { background: var(--brand-soft); color: var(--brand-2); border-color: #FFD9B5; }
    .st-contacted { background: var(--blue-soft); color: var(--blue); border-color: #C9DAFF; }
    .st-completed { background: var(--green-soft); color: var(--green); border-color: #BFE6CF; }
    .st-cancelled { background: #F1F5F9; color: var(--muted); }
    .pager { display: flex; justify-content: space-between; align-items: center; padding-top: 14px; border-top: 1px solid var(--line-2); margin-top: 6px; }
    @media (max-width: 1100px) { .stats { grid-template-columns: repeat(2, 1fr); } .dash-row { grid-template-columns: 1fr; } }
    @media (max-width: 700px) {
        .stats { gap: 10px; } .stat { padding: 14px; } .stat-ic { width: 38px; height: 38px; } .stat-n { font-size: 22px; }
        .search { min-width: 0; width: 100%; }
        .leads thead { display: none; }
        .leads, .leads tbody, .leads tr, .leads td { display: block; width: 100%; }
        .leads tr { border: 1px solid var(--line); border-radius: 12px; margin-bottom: 12px; padding: 4px 0; }
        .leads td { border: 0; padding: 8px 14px; }
        .leads tr.is-new { border-left: 3px solid var(--brand); }
        .leads tr.is-new td:first-child { box-shadow: none; }
        .leads .row-actions { justify-content: flex-start !important; }
        .chart { gap: 4px; }
    }
</style>
<?php adminFoot(); ?>
