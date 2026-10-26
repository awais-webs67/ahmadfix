<?php
/**
 * Ahmad Fix — Enterprise Admin Control Panel & Business Intelligence Dashboard
 */

require_once __DIR__ . '/auth.php';

// ============================================
// CSV Lead Export Handler
// ============================================
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=AhmadFix_Leads_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    // Output UTF-8 BOM for Excel Arabic support
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, ['Lead ID', 'Customer Name', 'Phone Number', 'Email', 'Service Requested', 'Customer Message', 'Status', 'Submitted Date']);
    
    $export_stmt = $pdo->query("SELECT * FROM contact_submissions ORDER BY submitted_at DESC");
    while ($row = $export_stmt->fetch()) {
        fputcsv($output, [
            $row['id'],
            $row['customer_name'],
            $row['phone'],
            $row['email'] ?: 'Not Provided',
            $row['service_requested'],
            $row['message'],
            strtoupper($row['status'] ?? ($row['is_read'] ? 'read' : 'new')),
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type'])) {
    if ($_POST['action_type'] === 'update_settings') {
        $new_phone = trim($_POST['site_phone'] ?? '');
        $new_wa = trim($_POST['site_wa'] ?? '');
        $new_email = trim($_POST['site_email'] ?? '');
        
        if ($new_phone && $new_email) {
            $pdo->prepare("UPDATE site_settings SET setting_value_en = :val, setting_value_ar = :val WHERE setting_key = 'phone'")->execute([':val' => $new_phone]);
            $pdo->prepare("UPDATE site_settings SET setting_value_en = :val, setting_value_ar = :val WHERE setting_key = 'whatsapp'")->execute([':val' => $new_wa]);
            $pdo->prepare("UPDATE site_settings SET setting_value_en = :val, setting_value_ar = :val WHERE setting_key = 'email'")->execute([':val' => $new_email]);
            $msg = 'Site contact settings updated successfully!';
        }
    }
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action_id = (int)$_GET['id'];
    $act = $_GET['action'];
    
    if ($act === 'status' && isset($_GET['val'])) {
        $new_status = trim($_GET['val']);
        $is_read = ($new_status === 'new') ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE contact_submissions SET status = :st, is_read = :rd WHERE id = :id");
        $stmt->execute([':st' => $new_status, ':rd' => $is_read, ':id' => $action_id]);
        $msg = 'Lead #' . $action_id . ' status updated to "' . strtoupper($new_status) . '".';
    } elseif ($act === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM contact_submissions WHERE id = :id");
        $stmt->execute([':id' => $action_id]);
        $msg = 'Lead #' . $action_id . ' deleted successfully.';
        $msg_type = 'warning';
    }
}

// Search and Filter logic
$search = trim($_GET['q'] ?? '');
$filter_status = trim($_GET['status'] ?? 'all');

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
        .user-nav { display: flex; align-items: center; gap: 16px; }
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
        
        .empty-state { padding: 60px 20px; text-align: center; color: #64748b; font-size: 16px; }
    </style>
</head>
<body>

<header>
    <a href="<?php echo SITE_URL; ?>" class="brand" target="_blank">
        <img src="<?php echo ASSETS_URL; ?>/images/logo.png" alt="Ahmad Fix">
        <h1>Ahmad Fix Enterprise</h1>
    </a>
    <div class="user-nav">
        <span class="user-name">👤 <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
        <button onclick="document.getElementById('settingsModal').style.display='flex';" class="btn-header btn-settings">⚙️ Site Settings</button>
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
                            $cust_name = htmlspecialchars($sub['customer_name']);
                            $svc_key = strtolower($sub['service_requested']);
                            $svc_name = $service_labels[$svc_key] ?? $sub['service_requested'] ?: 'General Inquiry';
                            $curr_status = $sub['status'] ?? ($sub['is_read'] ? 'contacted' : 'new');
                            
                            // Pre-fill WhatsApp greeting message
                            $wa_msg = urlencode("Hello {$cust_name}, thank you for reaching out to Ahmad Fix! We received your request for {$svc_name}. Our technician is ready to assist you.");
                            $wa_url = "https://wa.me/" . ltrim($clean_phone, '+') . "?text=" . $wa_msg;
                        ?>
                            <tr class="<?php echo ($curr_status === 'new') ? 'unread' : ''; ?>">
                                <td><strong>#<?php echo $sub['id']; ?></strong></td>
                                <td>
                                    <strong style="color: #fff; font-size: 15px;"><?php echo $cust_name; ?></strong>
                                    <?php if (!empty($sub['email'])): ?>
                                        <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;"><?php echo htmlspecialchars($sub['email']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="tel:<?php echo $clean_phone; ?>" style="color: #ff7a00; font-weight: 800; text-decoration: none;">
                                        📞 <?php echo htmlspecialchars($sub['phone']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span style="background: rgba(255, 122, 0, 0.12); color: #ff7a00; font-weight: 700; padding: 5px 10px; border-radius: 6px; font-size: 12px;">
                                        <?php echo htmlspecialchars($svc_name); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="color: #cbd5e1; font-size: 13px; max-width: 250px; word-break: break-word;">
                                        <?php echo !empty($sub['message']) ? htmlspecialchars($sub['message']) : '<em style="color:#64748b;">No text message</em>'; ?>
                                    </div>
                                </td>
                                <td>
                                    <select class="status-select" onchange="window.location.href='?action=status&id=<?php echo $sub['id']; ?>&val=' + this.value">
                                        <option value="new" <?php echo $curr_status === 'new' ? 'selected' : ''; ?>>⚡ NEW</option>
                                        <option value="contacted" <?php echo $curr_status === 'contacted' ? 'selected' : ''; ?>>🛠️ DISPATCHED</option>
                                        <option value="completed" <?php echo $curr_status === 'completed' ? 'selected' : ''; ?>>✅ COMPLETED</option>
                                        <option value="cancelled" <?php echo $curr_status === 'cancelled' ? 'selected' : ''; ?>>❌ CANCELLED</option>
                                    </select>
                                </td>
                                <td style="color: #94a3b8; font-size: 13px;">
                                    <?php echo date('M d, Y', strtotime($sub['submitted_at'])); ?><br>
                                    <small style="color: #64748b;"><?php echo date('h:i A', strtotime($sub['submitted_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="<?php echo $wa_url; ?>" target="_blank" class="btn-act btn-wa" title="Reply on WhatsApp with pre-filled greeting">💬 WhatsApp</a>
                                        <a href="tel:<?php echo $clean_phone; ?>" class="btn-act btn-call" title="Call Customer">📞 Call</a>
                                        <a href="?action=delete&id=<?php echo $sub['id']; ?>" onclick="return confirm('Delete lead #<?php echo $sub['id']; ?>?');" class="btn-act btn-del" title="Delete Lead">🗑️</a>
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

<!-- Quick Settings Modal -->
<div class="modal-overlay" id="settingsModal">
    <div class="modal-card">
        <div class="modal-header">
            <h3>⚙️ Site Contact Settings</h3>
            <button class="modal-close" onclick="document.getElementById('settingsModal').style.display='none';">&times;</button>
        </div>
        <form method="POST" action="" class="modal-body">
            <input type="hidden" name="action_type" value="update_settings">
            <div class="form-group">
                <label>Business Phone Number</label>
                <input type="text" name="site_phone" value="<?php echo htmlspecialchars(PHONE); ?>" required>
            </div>
            <div class="form-group">
                <label>WhatsApp Number (without +)</label>
                <input type="text" name="site_wa" value="<?php echo htmlspecialchars(WHATSAPP); ?>" required>
            </div>
            <div class="form-group">
                <label>Admin Notification Email</label>
                <input type="email" name="site_email" value="<?php echo htmlspecialchars(EMAIL); ?>" required>
            </div>
            <button type="submit" class="btn-header btn-export" style="width: 100%; justify-content: center; padding: 12px; margin-top: 15px;">Save Changes</button>
        </form>
    </div>
</div>

</body>
</html>
