<?php
/**
 * Ahmad Fix — Enterprise Admin Control Panel & Business Intelligence Dashboard
 */

require_once __DIR__ . '/auth.php';

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
    $where_clauses[] = "(customer_name LIKE :q OR phone LIKE :q OR email LIKE :q OR service_requested LIKE :q OR message LIKE :q)";
    $params[':q'] = '%' . $search . '%';
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
$total_leads = $pdo->query("SELECT COUNT(*) FROM contact_submissions")->fetchColumn();
$new_leads = $pdo->query("SELECT COUNT(*) FROM contact_submissions WHERE is_read = 0 OR status = 'new'")->fetchColumn();
$dispatched_leads = $pdo->query("SELECT COUNT(*) FROM contact_submissions WHERE status = 'contacted'")->fetchColumn();
$completed_leads = $pdo->query("SELECT COUNT(*) FROM contact_submissions WHERE status = 'completed'")->fetchColumn();

// Fetch filtered list
$stmt = $pdo->prepare("SELECT * FROM contact_submissions $where_sql ORDER BY submitted_at DESC");
$stmt->execute($params);
$submissions = $stmt->fetchAll();

// Keep the current search/filter when a lead is updated
$back_query = '?' . http_build_query(array_filter([
    'q' => $search,
    'status' => $filter_status !== 'all' ? $filter_status : null,
]));

$service_labels = [
    'ac' => 'AC Repair & Maintenance',
    'fridge' => 'Refrigerator Repair',
    'washer' => 'Washing Machine Repair',
    'oven' => 'Oven & Stove Repair',
    'other' => 'General Inquiry'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Enterprise Admin Panel — Ahmad Fix Dashboard</title>
    <link rel="icon" type="image/png" href="<?php echo ASSETS_URL; ?>/images/favicon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #0b0f19; color: #f8fafc; min-height: 100vh; padding-bottom: 50px; }
        
        /* Top Navigation Header */
        header { background: #151d2a; border-bottom: 1px solid #233044; padding: 16px 30px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; }
        .brand { display: flex; align-items: center; gap: 14px; text-decoration: none; color: #fff; }
        .brand img { max-height: 44px; }
        .brand h1 { font-size: 20px; font-weight: 800; color: #ff7a00; letter-spacing: -0.5px; }
        .user-nav { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .user-name { font-size: 14px; color: #94a3b8; font-weight: 600; background: #0b0f19; padding: 6px 14px; border-radius: 20px; border: 1px solid #233044; }
        .btn-header { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; border: none; }
        .btn-settings { background: #1e293b; color: #38bdf8; border: 1px solid #0284c7; }
        .btn-settings:hover { background: #0284c7; color: #fff; }
        .btn-export { background: #065f46; color: #34d399; border: 1px solid #059669; }
        .btn-export:hover { background: #059669; color: #fff; }
        .btn-logout { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid #ef4444; }
        .btn-logout:hover { background: #ef4444; color: #fff; }
        
        .container { max-width: 1320px; margin: 30px auto; padding: 0 20px; }
        
        /* Business Benefits / Stats Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #151d2a; border: 1px solid #233044; border-radius: 16px; padding: 24px; position: relative; overflow: hidden; }
        .stat-card::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: #334155; }
        .stat-card.orange::before { background: #ff7a00; }
        .stat-card.blue::before { background: #38bdf8; }
        .stat-card.green::before { background: #34d399; }
        .stat-card.purple::before { background: #a855f7; }
        
        .stat-title { font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; }
        .stat-value { font-size: 34px; font-weight: 900; color: #fff; margin-top: 8px; }
        .stat-desc { font-size: 12px; color: #64748b; margin-top: 6px; }

        .toast-msg { background: rgba(52, 211, 153, 0.12); color: #34d399; border: 1px solid #059669; padding: 14px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .toast-msg.warning { background: rgba(239, 68, 68, 0.12); color: #fca5a5; border-color: #dc2626; }

        /* Search & Filter Toolbar */
        .toolbar-card { background: #151d2a; border: 1px solid #233044; border-radius: 16px; padding: 20px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
        .search-box { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 280px; }
        .search-box input { width: 100%; padding: 12px 16px; background: #0b0f19; border: 1px solid #233044; border-radius: 10px; color: #fff; font-size: 14px; outline: none; transition: 0.2s; }
        .search-box input:focus { border-color: #ff7a00; }
        
        .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
        .filter-btn { padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; color: #94a3b8; background: #0b0f19; border: 1px solid #233044; transition: 0.2s; }
        .filter-btn:hover, .filter-btn.active { background: #ff7a00; color: #fff; border-color: #ff7a00; }

        /* Data Table */
        .table-card { background: #151d2a; border: 1px solid #233044; border-radius: 16px; overflow: hidden; box-shadow: 0 15px 25px -5px rgba(0,0,0,0.4); }
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }

        table { width: 100%; border-collapse: collapse; text-align: left; min-width: 850px; }
        th { background: #0b0f19; color: #94a3b8; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; padding: 18px 20px; border-bottom: 1px solid #233044; }
        td { padding: 18px 20px; border-bottom: 1px solid #233044; font-size: 14px; vertical-align: middle; }
        tr.unread { background: rgba(255, 122, 0, 0.04); }
        tr:hover { background: rgba(255, 255, 255, 0.02); }

        .badge-status { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
        .status-new { background: rgba(255, 122, 0, 0.15); color: #ff7a00; border: 1px solid #ff7a00; }
        .status-contacted { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid #0284c7; }
        .status-completed { background: rgba(52, 211, 153, 0.15); color: #34d399; border: 1px solid #059669; }
        .status-cancelled { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid #ef4444; }

        .actions { display: flex; align-items: center; gap: 8px; }
        .btn-act { padding: 8px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px; border: none; cursor: pointer; }
        .btn-wa { background: #25D366; color: #fff; }
        .btn-wa:hover { background: #1eb956; box-shadow: 0 4px 12px rgba(37,211,102,0.3); }
        .btn-call { background: #0284c7; color: #fff; }
        .btn-call:hover { background: #0369a1; }
        .btn-del { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
        .btn-del:hover { background: #ef4444; color: #fff; }

        select.status-select { background: #0b0f19; color: #fff; border: 1px solid #334155; padding: 6px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; }

        /* Settings Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 2000; display: none; align-items: center; justify-content: center; padding: 20px; }
        .modal-card { background: #151d2a; border: 1px solid #233044; border-radius: 16px; width: 100%; max-width: 540px; padding: 30px; }
        .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #233044; padding-bottom: 15px; }
        .modal-header h3 { font-size: 18px; font-weight: 800; color: #fff; }
        .modal-close { background: none; border: none; color: #94a3b8; font-size: 20px; cursor: pointer; }
        .modal-body .form-group { margin-bottom: 16px; }
        .modal-body label { display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; }
        .modal-body input { width: 100%; padding: 12px; background: #0b0f19; border: 1px solid #334155; border-radius: 8px; color: #fff; font-size: 14px; outline: none; }
        .modal-body input:focus { border-color: #ff7a00; }
        .inline-form { display: inline; margin: 0; }
        .force-card { max-width: 480px; margin: 80px auto; background: #151d2a; border: 1px solid #233044; border-radius: 16px; padding: 30px; }
        .force-card h2 { font-size: 20px; color: #fff; margin-bottom: 8px; }
        .force-card p { color: #94a3b8; font-size: 14px; margin-bottom: 20px; line-height: 1.6; }
        .force-msg { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #fca5a5; padding: 12px; border-radius: 8px; font-size: 14px; margin-bottom: 16px; }
        
        .empty-state { padding: 60px 20px; text-align: center; color: #64748b; font-size: 16px; }
        @media (max-width: 720px) {
            header { padding: 14px 16px; }
            .brand h1 { font-size: 17px; }
            .brand img { max-height: 36px; }
            .user-nav { width: 100%; display: grid; grid-template-columns: 1fr 1fr; }
            .user-nav .user-name { grid-column: 1 / -1; text-align: center; }
            .user-nav .btn-header { justify-content: center; }
            .container { margin: 20px auto; padding: 0 12px; }
            .stats-grid { grid-template-columns: 1fr 1fr !important; gap: 12px !important; }
            .stat-card { padding: 16px !important; }
            .stat-value { font-size: 28px !important; }
            .toolbar-card { padding: 14px; }
            .search-box, .search-box input { width: 100%; }
        }
    </style>
</head>
<body>
<?php if ($must_change_password): ?>
<!-- First login with the default password: a new password is required -->
<div class="force-card">
    <h2>🔒 Set a new admin password</h2>
    <p>You signed in with the default password. Please choose your own password now — the default one will stop working after this.</p>
    <?php if ($msg): ?><div class="force-msg"><?php echo e($msg); ?></div><?php endif; ?>
    <form method="POST" action="" class="modal-body">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <input type="hidden" name="action_type" value="change_password">
        <div class="form-group">
            <label>New Password (min. 10 characters)</label>
            <input type="password" name="new_password" autocomplete="new-password" minlength="10" required autofocus>
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" autocomplete="new-password" minlength="10" required>
        </div>
        <button type="submit" class="btn-header btn-export" style="width: 100%; justify-content: center; padding: 12px;">Save New Password</button>
    </form>
    <p style="margin-top: 16px; margin-bottom: 0;"><a href="<?php echo SITE_URL; ?>/admin/logout" style="color: #f87171;">Sign out</a></p>
</div>
</body>
</html>
<?php return; endif; ?>


<header>
    <a href="<?php echo SITE_URL; ?>" class="brand" target="_blank">
        <img src="<?php echo ASSETS_URL; ?>/images/logo-header.png" alt="<?php echo e(SITE_NAME); ?>" style="background:#fff;border-radius:6px;padding:3px 6px;">
        <h1><?php echo e(SITE_NAME); ?> Admin</h1>
    </a>
    <div class="user-nav">
        <span class="user-name">👤 <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
        <a href="<?php echo SITE_URL; ?>/admin/emails" class="btn-header btn-settings">✉️ Emails</a>
        <a href="<?php echo SITE_URL; ?>/admin/reviews" class="btn-header btn-settings">⭐ Reviews</a>
        <a href="<?php echo SITE_URL; ?>/admin/settings" class="btn-header btn-settings">⚙️ Settings</a>
        <a href="?export=csv" class="btn-header btn-export">📥 Export CSV</a>
        <a href="<?php echo SITE_URL; ?>/admin/logout" class="btn-header btn-logout">Sign Out</a>
    </div>
</header>

<div class="container">

    <?php if ($msg): ?>
        <div class="toast-msg <?php echo $msg_type === 'warning' ? 'warning' : ''; ?>">
            ✓ <?php echo htmlspecialchars($msg); ?>
        </div>
    <?php endif; ?>

    <!-- Business Intelligence Stats -->
    <div class="stats-grid">
        <div class="stat-card purple">
            <div class="stat-title">Total Inquiries</div>
            <div class="stat-value"><?php echo $total_leads; ?></div>
            <div class="stat-desc">Lifetime customer leads received</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-title">New Pending Leads</div>
            <div class="stat-value"><?php echo $new_leads; ?></div>
            <div class="stat-desc">Requires immediate dispatch</div>
        </div>
        <div class="stat-card blue">
            <div class="stat-title">Technician Dispatched</div>
            <div class="stat-value"><?php echo $dispatched_leads; ?></div>
            <div class="stat-desc">Jobs currently in progress</div>
        </div>
        <div class="stat-card green">
            <div class="stat-title">Completed Repairs</div>
            <div class="stat-value"><?php echo $completed_leads; ?></div>
            <div class="stat-desc">Successfully serviced & paid</div>
        </div>
    </div>

    <!-- Search & Filter Toolbar -->
    <div class="toolbar-card">
        <form method="GET" action="" class="search-box">
            <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="🔍 Search leads by name, phone, or service...">
            <?php if ($filter_status !== 'all'): ?>
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($filter_status); ?>">
            <?php endif; ?>
        </form>
        
        <div class="filter-tabs">
            <a href="?status=all<?php echo $search ? '&q='.urlencode($search) : ''; ?>" class="filter-btn <?php echo $filter_status === 'all' ? 'active' : ''; ?>">All (<?php echo $total_leads; ?>)</a>
            <a href="?status=new<?php echo $search ? '&q='.urlencode($search) : ''; ?>" class="filter-btn <?php echo $filter_status === 'new' ? 'active' : ''; ?>">New Leads (<?php echo $new_leads; ?>)</a>
            <a href="?status=contacted<?php echo $search ? '&q='.urlencode($search) : ''; ?>" class="filter-btn <?php echo $filter_status === 'contacted' ? 'active' : ''; ?>">Dispatched</a>
            <a href="?status=completed<?php echo $search ? '&q='.urlencode($search) : ''; ?>" class="filter-btn <?php echo $filter_status === 'completed' ? 'active' : ''; ?>">Completed</a>
        </div>
    </div>

    <!-- Leads Table Card -->
    <div class="table-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Lead ID</th>
                        <th>Customer Details</th>
                        <th>Phone & Direct Action</th>
                        <th>Service Requested</th>
                        <th>Issue Notes</th>
                        <th>Pipeline Status</th>
                        <th>Submitted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    📥 No leads found matching your filter criteria.
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $sub): 
                            $clean_phone = preg_replace('/[^+0-9]/', '', $sub['phone']);
                            $cust_name = e($sub['customer_name']);
                            $svc_key = strtolower($sub['service_requested']);
                            $svc_name = serviceLabel($svc_key, 'en');
                            $curr_status = $sub['status'] ?? ($sub['is_read'] ? 'contacted' : 'new');
                            
                            // Pre-fill WhatsApp greeting message
                            $wa_msg = rawurlencode("Hello " . html_entity_decode($cust_name, ENT_QUOTES, 'UTF-8') . ", thank you for reaching out to " . SITE_NAME . "! We received your request for {$svc_name}. Our technician is ready to assist you.");
                            $wa_url = "https://wa.me/" . customerWhatsApp($sub['phone']) . "?text=" . $wa_msg;
                        ?>
                            <tr class="<?php echo ($curr_status === 'new') ? 'unread' : ''; ?>">
                                <td><strong>#<?php echo $sub['id']; ?></strong></td>
                                <td>
                                    <a href="<?php echo SITE_URL; ?>/admin/lead?id=<?php echo (int)$sub['id']; ?>" style="color: #fff; font-size: 15px; font-weight: 700; text-decoration: none;"><?php echo $cust_name; ?></a>
                                    <?php if (!empty($sub['email'])): ?>
                                        <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;"><?php echo e($sub['email']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="tel:<?php echo $clean_phone; ?>" style="color: #ff7a00; font-weight: 800; text-decoration: none;">
                                        📞 <?php echo e($sub['phone']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span style="background: rgba(255, 122, 0, 0.12); color: #ff7a00; font-weight: 700; padding: 5px 10px; border-radius: 6px; font-size: 12px;">
                                        <?php echo e($svc_name); ?>
                                    </span>
                                    <?php if (!empty($sub['city']) || !empty($sub['preferred_date'])): ?>
                                    <div style="font-size: 12px; color: #94a3b8; margin-top: 8px; line-height: 1.6;">
                                        <?php if (!empty($sub['city'])): ?>📍 <?php echo e($sub['city']); ?><br><?php endif; ?>
                                        <?php if (!empty($sub['preferred_date'])): ?>🗓️ <?php echo e(date('D j M', strtotime($sub['preferred_date']))); ?><?php echo !empty($sub['preferred_time']) ? ' · ' . e(ucfirst($sub['preferred_time'])) : ''; ?><?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="color: #cbd5e1; font-size: 13px; max-width: 250px; word-break: break-word;">
                                        <?php echo !empty($sub['message']) ? e($sub['message']) : '<em style="color:#64748b;">No text message</em>'; ?>
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" action="" class="inline-form">
                                    <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
                                    <input type="hidden" name="action_type" value="lead_action">
                                    <input type="hidden" name="act" value="status">
                                    <input type="hidden" name="id" value="<?php echo (int)$sub['id']; ?>">
                                    <input type="hidden" name="back" value="<?php echo e($back_query); ?>">
                                    <select class="status-select" name="val" onchange="this.form.submit()" aria-label="Lead status">
                                        <option value="new" <?php echo $curr_status === 'new' ? 'selected' : ''; ?>>⚡ NEW</option>
                                        <option value="contacted" <?php echo $curr_status === 'contacted' ? 'selected' : ''; ?>>🛠️ DISPATCHED</option>
                                        <option value="completed" <?php echo $curr_status === 'completed' ? 'selected' : ''; ?>>✅ COMPLETED</option>
                                        <option value="cancelled" <?php echo $curr_status === 'cancelled' ? 'selected' : ''; ?>>❌ CANCELLED</option>
                                    </select>
                                    </form>
                                </td>
                                <td style="color: #94a3b8; font-size: 13px;">
                                    <?php echo date('M d, Y', strtotime($sub['submitted_at'])); ?><br>
                                    <small style="color: #64748b;"><?php echo date('h:i A', strtotime($sub['submitted_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="<?php echo SITE_URL; ?>/admin/lead?id=<?php echo (int)$sub['id']; ?>" class="btn-act btn-call" title="Open lead, reply by email">👁️ View / Reply</a>
                                        <a href="<?php echo e($wa_url); ?>" target="_blank" rel="noopener" class="btn-act btn-wa" title="Reply on WhatsApp with pre-filled greeting">💬 WhatsApp</a>
                                        <a href="tel:<?php echo $clean_phone; ?>" class="btn-act btn-call" title="Call Customer">📞 Call</a>
                                        <form method="POST" action="" class="inline-form" onsubmit="return confirm('Delete lead #<?php echo (int)$sub['id']; ?>?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
                                            <input type="hidden" name="action_type" value="lead_action">
                                            <input type="hidden" name="act" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int)$sub['id']; ?>">
                                            <input type="hidden" name="back" value="<?php echo e($back_query); ?>">
                                            <button type="submit" class="btn-act btn-del" title="Delete Lead">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
