<?php
/**
 * Ahmad Fix — Admin: Emails (write a new email + history of everything sent)
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/_layout.php';

if (!empty($_SESSION['must_change_password'])) {
    header('Location: ' . SITE_URL . '/admin/index');
    exit;
}

$send_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        flash('Security check failed. Please try again.', 'error');
        header('Location: ' . SITE_URL . '/admin/emails');
        exit;
    }
    $to = trim($_POST['to'] ?? '');
    $name = trim($_POST['to_name'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $mlang = ($_POST['mlang'] ?? 'en') === 'ar' ? 'ar' : 'en';
    if (!filter_var($to, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
        $send_error = 'Please enter a valid email address, a subject and a message.';
    } else {
        $r = sendCustomerEmail($to, $name, $subject, $message, $mlang, null, $_SESSION['admin_name'] ?? 'Admin', false);
        if ($r['ok']) {
            flash('Email sent to ' . $to . '.');
            header('Location: ' . SITE_URL . '/admin/emails');
            exit;
        }
        $send_error = 'Email not sent: ' . $r['error'];
    }
}

// Filters + paging
$type = $_GET['type'] ?? 'all';
$status = $_GET['status'] ?? 'all';
$types = ['all' => 'All', 'notification' => 'Lead alerts', 'auto_reply' => 'Automatic thank-you', 'reply' => 'Replies', 'compose' => 'New emails', 'test' => 'Tests'];
if (!isset($types[$type])) $type = 'all';
if (!in_array($status, ['all', 'sent', 'failed'], true)) $status = 'all';
$where = []; $params = [];
if ($type !== 'all') { $where[] = 'type = :t'; $params[':t'] = $type; }
if ($status !== 'all') { $where[] = 'status = :s'; $params[':s'] = $status; }
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$page = max(1, (int)($_GET['p'] ?? 1));
$per = 50;
$count = $pdo->prepare("SELECT COUNT(*) FROM email_log $where_sql"); $count->execute($params);
$total = (int)$count->fetchColumn();
$list = $pdo->prepare("SELECT * FROM email_log $where_sql ORDER BY created_at DESC, id DESC LIMIT $per OFFSET " . (($page - 1) * $per));
$list->execute($params);
$rows = $list->fetchAll();
$failed_count = (int)$pdo->query("SELECT COUNT(*) FROM email_log WHERE status = 'failed' AND created_at > NOW() - INTERVAL 7 DAY")->fetchColumn();
$q = fn($extra) => '?' . http_build_query(array_filter(array_merge(['type' => $type !== 'all' ? $type : null, 'status' => $status !== 'all' ? $status : null], $extra)));

adminHead('Emails', 'emails');
?>
<h1 class="a-title">✉️ Emails</h1>
<p class="a-sub">Write to any customer, and see every email the website has sent — lead alerts to you, automatic thank-you emails and your replies.
Replies from customers arrive in your normal inbox (<strong><?php echo e(EMAIL); ?></strong>).</p>

<?php adminFlash(); ?>
<?php if ($send_error): ?><div class="toast error"><?php echo e($send_error); ?></div><?php endif; ?>
<?php if (!mailEnabled()): ?>
    <div class="toast warning">Email is not set up yet, so nothing can be sent. <a href="<?php echo SITE_URL; ?>/admin/settings#email">Open Settings → Email</a>.</div>
<?php elseif ($failed_count): ?>
    <div class="toast warning"><?php echo $failed_count; ?> email(s) failed in the last 7 days. <a href="<?php echo SITE_URL; ?>/admin/emails?status=failed">See why</a> — usually a wrong SMTP password.</div>
<?php endif; ?>

<div class="mail-grid">
    <form method="POST" action="<?php echo SITE_URL; ?>/admin/emails" class="card">
        <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
        <h2>Write a new email</h2>
        <p class="hint">Uses your logo, colours and signature. To reply to a lead, open the lead instead — it adds their request automatically.</p>
        <div class="fg"><label>To (email)</label><input type="email" name="to" value="<?php echo e($_POST['to'] ?? ($_GET['to'] ?? '')); ?>" required></div>
        <div class="grid2">
            <div class="fg"><label>Name</label><input type="text" name="to_name" value="<?php echo e($_POST['to_name'] ?? ''); ?>"></div>
            <div class="fg"><label>Language</label>
                <select name="mlang" onchange="document.getElementById('cmsg').dir = this.value === 'ar' ? 'rtl' : 'ltr';">
                    <option value="en">English</option>
                    <option value="ar" <?php echo ($_POST['mlang'] ?? '') === 'ar' ? 'selected' : ''; ?>>العربية (Arabic)</option>
                </select></div>
        </div>
        <div class="fg"><label>Subject</label><input type="text" name="subject" value="<?php echo e($_POST['subject'] ?? ''); ?>" required></div>
        <div class="fg"><label>Message</label><textarea id="cmsg" name="message" rows="8" required><?php echo e($_POST['message'] ?? ''); ?></textarea></div>
        <button type="submit" class="btn btn-orange" <?php echo mailEnabled() ? '' : 'disabled'; ?>>Send email</button>
    </form>

    <div class="card">
        <h2>Sent emails (<?php echo $total; ?>)</h2>
        <div class="row-actions" style="margin: 12px 0 14px;">
            <?php foreach ($types as $tk => $tl): ?>
                <a href="<?php echo e($q(['type' => $tk !== 'all' ? $tk : null, 'p' => null])); ?>" class="btn btn-sm <?php echo $type === $tk ? 'btn-orange' : 'btn-gray'; ?>"><?php echo e($tl); ?></a>
            <?php endforeach; ?>
            <a href="<?php echo e($q(['status' => $status === 'failed' ? null : 'failed', 'p' => null])); ?>" class="btn btn-sm <?php echo $status === 'failed' ? 'btn-red' : 'btn-gray'; ?>">Failed only</a>
        </div>
        <div class="table-wrap">
        <table>
            <thead><tr><th>When</th><th>To</th><th>Subject</th><th>Type</th><th>Status</th></tr></thead>
            <tbody>
            <?php if (!$rows): ?><tr><td colspan="5" class="muted">Nothing here yet.</td></tr><?php endif; ?>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td class="muted" style="white-space: nowrap;"><?php echo e(date('j M, g:i A', strtotime($r['created_at']))); ?></td>
                    <td><?php echo e($r['to_email']); ?><?php if ($r['lead_id']): ?><br><a class="muted" href="<?php echo SITE_URL; ?>/admin/lead?id=<?php echo (int)$r['lead_id']; ?>">Lead #<?php echo (int)$r['lead_id']; ?></a><?php endif; ?></td>
                    <td><?php echo e($r['subject']); ?>
                        <?php if ($r['message'] && in_array($r['type'], ['reply', 'compose'], true)): ?><details><summary class="muted">Message</summary><div style="font-size: 13px; color: #cbd5e1; margin-top: 6px;"><?php echo nl2br(e($r['message'])); ?></div></details><?php endif; ?>
                        <?php if ($r['error']): ?><div style="color: #f87171; font-size: 12px; margin-top: 4px;"><?php echo e($r['error']); ?></div><?php endif; ?></td>
                    <td class="muted"><?php echo e($types[$r['type']] ?? $r['type']); ?></td>
                    <td><span class="badge b-<?php echo e($r['status']); ?>"><?php echo e($r['status']); ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php if ($total > $per): ?>
        <div class="row-actions" style="margin-top: 14px;">
            <?php if ($page > 1): ?><a class="btn btn-gray btn-sm" href="<?php echo e($q(['p' => $page - 1])); ?>">← Newer</a><?php endif; ?>
            <?php if ($page * $per < $total): ?><a class="btn btn-gray btn-sm" href="<?php echo e($q(['p' => $page + 1])); ?>">Older →</a><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .mail-grid { display: grid; grid-template-columns: 1fr 1.6fr; gap: 20px; align-items: start; }
    @media (max-width: 1000px) { .mail-grid { grid-template-columns: 1fr; } }
</style>
<?php adminFoot(); ?>
