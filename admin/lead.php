<?php
/**
 * Ahmad Fix — Admin: one lead (details, status, notes, email reply, history)
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/_layout.php';

if (!empty($_SESSION['must_change_password'])) {
    header('Location: ' . SITE_URL . '/admin/index');
    exit;
}

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM contact_submissions WHERE id = :id");
$stmt->execute([':id' => $id]);
$lead = $stmt->fetch();

if (!$lead) {
    flash('That lead was not found (it may have been deleted).', 'warning');
    header('Location: ' . SITE_URL . '/admin/index');
    exit;
}

// Opening a new lead marks it as read
if ((int)$lead['is_read'] === 0) {
    $pdo->prepare("UPDATE contact_submissions SET is_read = 1 WHERE id = :id")->execute([':id' => $id]);
}

$statuses = ['new' => '⚡ New', 'contacted' => '🛠️ Dispatched / contacted', 'completed' => '✅ Completed', 'cancelled' => '❌ Cancelled'];
$plain = fn($v) => html_entity_decode((string)$v, ENT_QUOTES, 'UTF-8');
$lead_lang = ($lead['lang'] ?? 'en') === 'ar' ? 'ar' : 'en';
$lead_email = $plain($lead['email']);
$send_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        flash('Security check failed. Please try again.', 'error');
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'status' && isset($statuses[$_POST['status'] ?? ''])) {
            $pdo->prepare("UPDATE contact_submissions SET status = :s, is_read = :r WHERE id = :id")
                ->execute([':s' => $_POST['status'], ':r' => $_POST['status'] === 'new' ? 0 : 1, ':id' => $id]);
            flash('Status updated.');
        } elseif ($action === 'notes') {
            $pdo->prepare("UPDATE contact_submissions SET notes = :n WHERE id = :id")
                ->execute([':n' => mb_substr(trim($_POST['notes'] ?? ''), 0, 5000), ':id' => $id]);
            flash('Notes saved.');
        } elseif ($action === 'send') {
            $to = trim($_POST['to'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $mlang = ($_POST['mlang'] ?? 'en') === 'ar' ? 'ar' : 'en';
            if (!filter_var($to, FILTER_VALIDATE_EMAIL) || $subject === '' || $message === '') {
                $send_error = 'Please enter a valid email address, a subject and a message.';
            } else {
                $r = sendCustomerEmail($to, $plain($lead['customer_name']), $subject, $message, $mlang, $lead, $_SESSION['admin_name'] ?? 'Admin', !empty($_POST['include_request']));
                if ($r['ok']) {
                    // Replying moves a new lead to "contacted"
                    if ($lead['status'] === 'new') {
                        $pdo->prepare("UPDATE contact_submissions SET status = 'contacted', is_read = 1 WHERE id = :id")->execute([':id' => $id]);
                    }
                    // Remember the address if the customer had none
                    if ($lead_email === '') {
                        $pdo->prepare("UPDATE contact_submissions SET email = :e WHERE id = :id")->execute([':e' => sanitize($to), ':id' => $id]);
                    }
                    flash('Email sent to ' . $to . '.');
                } else {
                    $send_error = 'Email not sent: ' . $r['error'];
                }
            }
        } elseif ($action === 'delete') {
            $pdo->prepare("DELETE FROM contact_submissions WHERE id = :id")->execute([':id' => $id]);
            flash('Lead #' . $id . ' deleted.', 'warning');
            header('Location: ' . SITE_URL . '/admin/index');
            exit;
        }
        if (!$send_error) {
            header('Location: ' . SITE_URL . '/admin/lead?id=' . $id);
            exit;
        }
    }
}

// Ready-made replies, filled in for this customer, in both languages
$templates = [];
foreach (['reply_tpl_confirm' => 'Confirm visit', 'reply_tpl_details' => 'Ask for details', 'reply_tpl_thanks' => 'Thank you after repair'] as $key => $label) {
    foreach (['en', 'ar'] as $lc) {
        $templates[$key][$lc] = fillTemplate(setting($key, $lc), leadVars($lead, $lc));
    }
    $templates[$key]['label'] = $label;
}
$default_subject = [
    'en' => 'Re: Your ' . serviceLabel($lead['service_requested'], 'en') . ' request — ' . SITE_NAME,
    'ar' => 'بخصوص طلب ' . serviceLabel($lead['service_requested'], 'ar') . ' — ' . SITE_NAME_AR,
];

$history = $pdo->prepare("SELECT * FROM email_log WHERE lead_id = :id ORDER BY created_at DESC, id DESC");
$history->execute([':id' => $id]);
$history = $history->fetchAll();

$tel = preg_replace('/[^+0-9]/', '', $lead['phone']);
$wa_text = rawurlencode(($lead_lang === 'ar' ? 'مرحباً ' : 'Hello ') . $plain($lead['customer_name']) . ($lead_lang === 'ar' ? '، معك ' . SITE_NAME_AR . ' بخصوص طلبك.' : ', this is ' . SITE_NAME . ' about your request.'));
$type_labels = ['notification' => 'Alert to you', 'auto_reply' => 'Automatic thank-you', 'reply' => 'Your reply', 'compose' => 'Email', 'test' => 'Test'];

adminHead('Lead #' . $id, 'index');
?>
<p style="margin-bottom: 12px;"><a href="<?php echo SITE_URL; ?>/admin/index" style="text-decoration: none;">← All leads</a></p>
<h1 class="a-title"><?php echo e($plain($lead['customer_name'])); ?> <span class="badge b-<?php echo e($lead['status']); ?>"><?php echo e($lead['status']); ?></span></h1>
<p class="a-sub">Lead #<?php echo $id; ?> · received <?php echo e(date('D, j M Y — g:i A', strtotime($lead['submitted_at']))); ?> · form language: <?php echo $lead_lang === 'ar' ? 'Arabic' : 'English'; ?></p>

<?php adminFlash(); ?>
<?php if ($send_error): ?><div class="toast error"><?php echo e($send_error); ?></div><?php endif; ?>

<div class="lead-grid">
    <div>
        <div class="card">
            <h2>Customer & request</h2>
            <p class="hint">&nbsp;</p>
            <table class="kv">
                <tr><th>Phone</th><td><a href="tel:<?php echo e($tel); ?>"><?php echo e($plain($lead['phone'])); ?></a></td></tr>
                <tr><th>Email</th><td><?php echo $lead_email ? '<a href="mailto:' . e($lead_email) . '">' . e($lead_email) . '</a>' : '<span class="muted">Not given</span>'; ?></td></tr>
                <tr><th>Service</th><td><?php echo e(serviceLabel($lead['service_requested'], 'en')); ?></td></tr>
                <tr><th>City</th><td><?php echo $lead['city'] ? e($lead['city']) : '<span class="muted">—</span>'; ?></td></tr>
                <tr><th>Address</th><td><?php echo $lead['address'] ? e($plain($lead['address'])) : '<span class="muted">—</span>'; ?></td></tr>
                <tr><th>Preferred date</th><td><?php echo $lead['preferred_date'] ? e(emailDate($lead['preferred_date'], 'en')) : '<span class="muted">Any</span>'; ?></td></tr>
                <tr><th>Preferred time</th><td><?php echo $lead['preferred_time'] ? e(timeSlotLabel($lead['preferred_time'], 'en')) : '<span class="muted">Any</span>'; ?></td></tr>
                <tr><th>Message</th><td><?php echo $lead['message'] ? nl2br(e($plain($lead['message']))) : '<span class="muted">—</span>'; ?></td></tr>
            </table>
            <div class="row-actions" style="margin-top: 16px;">
                <a class="btn btn-orange" href="tel:<?php echo e($tel); ?>">📞 Call</a>
                <a class="btn btn-green" target="_blank" rel="noopener" href="https://wa.me/<?php echo e(customerWhatsApp($lead['phone'])); ?>?text=<?php echo $wa_text; ?>">💬 WhatsApp</a>
            </div>
        </div>

        <!-- Email reply -->
        <form method="POST" action="<?php echo SITE_URL; ?>/admin/lead?id=<?php echo $id; ?>" class="card" id="reply">
            <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
            <input type="hidden" name="action" value="send">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <h2>✉️ Email the customer</h2>
            <p class="hint">Sent from your website email with your logo and signature. If the customer replies, it arrives in <strong><?php echo e(EMAIL); ?></strong>.</p>
            <?php if (!mailEnabled()): ?>
                <div class="toast warning">Email is not set up yet. <a href="<?php echo SITE_URL; ?>/admin/settings#email">Add your SMTP login in Settings → Email</a>.</div>
            <?php endif; ?>
            <div class="grid2">
                <div class="fg"><label>To</label><input type="email" name="to" value="<?php echo e($_POST['to'] ?? $lead_email); ?>" placeholder="customer@email.com" required></div>
                <div class="fg"><label>Language of the email</label>
                    <select name="mlang" id="mlang">
                        <option value="en" <?php echo (($_POST['mlang'] ?? $lead_lang) === 'en') ? 'selected' : ''; ?>>English</option>
                        <option value="ar" <?php echo (($_POST['mlang'] ?? $lead_lang) === 'ar') ? 'selected' : ''; ?>>العربية (Arabic)</option>
                    </select>
                </div>
            </div>
            <div class="fg"><label>Quick reply</label>
                <select id="tpl">
                    <option value="">— Write your own —</option>
                    <?php foreach ($templates as $tk => $t): ?><option value="<?php echo e($tk); ?>"><?php echo e($t['label']); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="fg"><label>Subject</label><input type="text" name="subject" id="subject" value="<?php echo e($_POST['subject'] ?? $default_subject[$lead_lang]); ?>" required></div>
            <div class="fg"><label>Message</label><textarea name="message" id="message" rows="8" <?php echo $lead_lang === 'ar' ? 'dir="rtl"' : ''; ?> required><?php echo e($_POST['message'] ?? ''); ?></textarea>
                <div class="help">Your signature is added automatically. Placeholders like {name} and {date} also work.</div></div>
            <label class="switch" style="margin-bottom: 16px;"><input type="checkbox" name="include_request" value="1" checked> Include a summary of their request</label>
            <button type="submit" class="btn btn-orange" <?php echo mailEnabled() ? '' : 'disabled'; ?>>Send email</button>
        </form>
    </div>

    <div>
        <form method="POST" action="<?php echo SITE_URL; ?>/admin/lead?id=<?php echo $id; ?>" class="card">
            <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
            <input type="hidden" name="action" value="status">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <h2>Status</h2>
            <p class="hint">&nbsp;</p>
            <select name="status" onchange="this.form.submit()">
                <?php foreach ($statuses as $sv => $sl): ?><option value="<?php echo $sv; ?>" <?php echo $lead['status'] === $sv ? 'selected' : ''; ?>><?php echo $sl; ?></option><?php endforeach; ?>
            </select>
        </form>

        <form method="POST" action="<?php echo SITE_URL; ?>/admin/lead?id=<?php echo $id; ?>" class="card">
            <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
            <input type="hidden" name="action" value="notes">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <h2>Private notes</h2>
            <p class="hint">Only you see these (price quoted, technician sent, parts…).</p>
            <textarea name="notes" rows="5"><?php echo e($lead['notes'] ?? ''); ?></textarea>
            <button type="submit" class="btn btn-gray" style="margin-top: 10px;">Save notes</button>
        </form>

        <div class="card">
            <h2>Email history</h2>
            <p class="hint">Every email sent about this lead.</p>
            <?php if (!$history): ?><p class="muted">No emails yet.</p><?php endif; ?>
            <?php foreach ($history as $h): ?>
            <div class="hist">
                <div class="row-actions" style="justify-content: space-between;">
                    <strong style="font-size: 13px;"><?php echo e($type_labels[$h['type']] ?? $h['type']); ?></strong>
                    <span class="badge b-<?php echo e($h['status']); ?>"><?php echo e($h['status']); ?></span>
                </div>
                <div class="muted">To <?php echo e($h['to_email']); ?> · <?php echo e(date('j M, g:i A', strtotime($h['created_at']))); ?></div>
                <div style="font-size: 13px; margin-top: 4px;"><?php echo e($h['subject']); ?></div>
                <?php if ($h['type'] === 'reply' && $h['message']): ?><details><summary class="muted">Show message</summary><div class="hist-msg"><?php echo nl2br(e($h['message'])); ?></div></details><?php endif; ?>
                <?php if ($h['error']): ?><div style="color: #f87171; font-size: 12px; margin-top: 4px;"><?php echo e($h['error']); ?></div><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" action="<?php echo SITE_URL; ?>/admin/lead?id=<?php echo $id; ?>" onsubmit="return confirm('Delete this lead permanently?');" style="text-align: right;">
            <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <button type="submit" class="btn btn-red btn-sm">🗑️ Delete lead</button>
        </form>
    </div>
</div>

<style>
    .lead-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; align-items: start; }
    .kv th { width: 34%; text-transform: none; font-size: 13px; letter-spacing: 0; color: #94a3b8; font-weight: 600; padding: 10px 12px 10px 0; vertical-align: top; }
    .kv td { padding: 10px 0; }
    .hist { border-top: 1px solid #233044; padding: 12px 0; }
    .hist:first-of-type { border-top: 0; }
    .hist-msg { background: #0b0f19; border-radius: 8px; padding: 10px; font-size: 13px; margin-top: 6px; color: #cbd5e1; }
    @media (max-width: 900px) { .lead-grid { grid-template-columns: 1fr; } }
</style>
<script>
(function () {
    var T = <?php echo json_encode($templates, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>;
    var S = <?php echo json_encode($default_subject, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>;
    var tpl = document.getElementById('tpl'), lang = document.getElementById('mlang'), msg = document.getElementById('message'), subj = document.getElementById('subject');
    function fill() {
        var l = lang.value;
        msg.dir = l === 'ar' ? 'rtl' : 'ltr';
        if (tpl.value && T[tpl.value]) msg.value = T[tpl.value][l];
    }
    tpl.addEventListener('change', fill);
    lang.addEventListener('change', function () {
        if (subj.value === S.en || subj.value === S.ar) subj.value = S[lang.value];
        fill();
    });
})();
</script>
<?php adminFoot(); ?>
