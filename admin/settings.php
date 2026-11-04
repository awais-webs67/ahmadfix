<?php
/**
 * Ahmad Fix — Admin: Settings
 * Every business detail, Google code, social link, SMTP login and email text.
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/_layout.php';

if (!empty($_SESSION['must_change_password'])) {
    header('Location: ' . SITE_URL . '/admin/index');
    exit;
}

$schema = settingsSchema();
$errors = [];
$test_result = null;

/**
 * Check and normalise one submitted value. Returns [value, error|null]
 */
function cleanSettingValue($f, $raw) {
    $v = trim(str_replace("\r\n", "\n", (string)$raw));
    switch ($f['type']) {
        case 'email':
            return ($v === '' || filter_var($v, FILTER_VALIDATE_EMAIL)) ? [$v, null] : [$v, 'is not a valid email address'];
        case 'url':
            if ($v === '') return ['', null];
            if (!preg_match('#^https?://#i', $v)) $v = 'https://' . $v;
            return filter_var($v, FILTER_VALIDATE_URL) ? [rtrim($v, '/') === $v ? $v : rtrim($v, '/'), null] : [$v, 'is not a valid link'];
        case 'number':
            return ($v === '' || ctype_digit($v)) ? [$v, null] : [$v, 'must be a number'];
        case 'time':
            return ($v === '' || preg_match('/^\d{2}:\d{2}$/', $v)) ? [$v, null] : [$v, 'must be a time like 08:00'];
        case 'select':
            return array_key_exists($v, $f['options']) ? [$v, null] : [array_key_first($f['options']), null];
        case 'toggle':
            return [$v === '1' ? '1' : '0', null];
        default:
            return [mb_substr($v, 0, 5000), null];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRF($_POST['csrf_token'] ?? '')) {
        flash('Security check failed. Please try again.', 'error');
        header('Location: ' . SITE_URL . '/admin/settings');
        exit;
    }
    $action = $_POST['action'] ?? '';
    $section = $_POST['section'] ?? '';

    if ($action === 'save' && isset($schema[$section])) {
        $to_save = [];
        foreach ($schema[$section]['fields'] as $key => $f) {
            // Set in your config file → not editable here
            if (mailFromConfigFile($key) !== null) {
                continue;
            }
            if ($f['type'] === 'password') {
                if (!empty($_POST[$key . '_clear'])) {
                    $to_save[$key] = ['', ''];
                } elseif (($_POST[$key] ?? '') !== '') {
                    // Gmail shows app passwords with spaces — remove them
                    $plain = trim($_POST[$key]);
                    if (stripos(opt('smtp_host'), 'gmail') !== false || stripos($_POST['smtp_host'] ?? '', 'gmail') !== false) {
                        $plain = str_replace(' ', '', $plain);
                    }
                    $enc = encryptSecret($plain);
                    $to_save[$key] = [$enc, $enc];
                }
                continue;
            }
            if (!empty($f['bilingual'])) {
                [$en, $e1] = cleanSettingValue($f, $_POST[$key . '_en'] ?? '');
                [$ar, $e2] = cleanSettingValue($f, $_POST[$key . '_ar'] ?? '');
                if ($e1 || $e2) $errors[] = $f['label'] . ' ' . ($e1 ?: $e2);
                $to_save[$key] = [$en, $ar];
            } else {
                $raw = $f['type'] === 'toggle' ? (isset($_POST[$key]) ? '1' : '0') : ($_POST[$key] ?? '');
                [$v, $err] = cleanSettingValue($f, $raw);
                if ($err) $errors[] = $f['label'] . ' ' . $err;
                $to_save[$key] = [$v, $v];
            }
        }
        // Website address: only scheme + host (+ optional folder), no trailing slash
        if (isset($to_save['site_url']) && $to_save['site_url'][0] !== '') {
            $u = parse_url($to_save['site_url'][0]);
            $clean = strtolower($u['scheme'] ?? 'https') . '://' . strtolower($u['host'] ?? '') . (isset($u['port']) ? ':' . $u['port'] : '') . rtrim($u['path'] ?? '', '/');
            $to_save['site_url'] = [$clean, $clean];
        }
        if (isset($to_save['notify_emails'])) {
            $list = array_filter(array_map('trim', preg_split('/[,;\s]+/', $to_save['notify_emails'][0])));
            $bad = array_filter($list, fn($x) => !filter_var($x, FILTER_VALIDATE_EMAIL));
            if ($bad) $errors[] = 'Lead alert address not valid: ' . implode(', ', $bad);
            $joined = implode(', ', $list);
            $to_save['notify_emails'] = [$joined, $joined];
        }

        if (!$errors) {
            foreach ($to_save as $k => [$en, $ar]) {
                saveSetting($k, $en, $ar, $section);
            }
            if ($section === 'email') {
                saveSetting('_smtp_blocked', '', '', 'system');   // re-test the server with the new settings
            }
            getAllSettings(true);
            flash($schema[$section]['title'] . ' saved.');
            header('Location: ' . SITE_URL . '/admin/settings#' . $section);
            exit;
        }
    } elseif ($action === 'test_email') {
        $to = trim($_POST['test_to'] ?? '');
        saveSetting('_smtp_blocked', '', '', 'system');   // the test always tries the real server first
        getAllSettings(true);
        $test_result = sendTestEmail($to);
        if ($test_result['ok']) {
            flash('Test email sent to ' . $to . '. Check the inbox (and the spam folder).'
                . (!empty($test_result['note']) ? ' Note: ' . $test_result['note'] . '.' : ''));
            header('Location: ' . SITE_URL . '/admin/settings#email');
            exit;
        }
    } elseif ($action === 'repair_arabic') {
        $r = repairArabic($pdo);
        $pdo = $GLOBALS['pdo'];
        getAllSettings(true);
        if ($r['ok']) {
            flash('Arabic repaired: ' . $r['restored'] . ' value(s) restored, and the database now saves Arabic correctly.');
        } else {
            flash('Arabic repair did not finish: ' . implode(' · ', $r['errors'] ?: ['still ' . $r['damaged_left'] . ' value(s) with question marks']), 'error');
        }
        header('Location: ' . SITE_URL . '/admin/settings#system');
        exit;
    } elseif ($action === 'page_seo') {
        $pages = ['home', 'about', 'contact', 'privacy-policy', 'terms'];
        $stmt = $pdo->prepare("INSERT INTO pages_seo (page_slug, title_en, title_ar, description_en, description_ar)
                               VALUES (:slug, :ten, :tar, :den, :dar)
                               ON DUPLICATE KEY UPDATE title_en = :ten2, title_ar = :tar2, description_en = :den2, description_ar = :dar2");
        foreach ($pages as $slug) {
            $vals = [];
            foreach (['title_en' => 200, 'title_ar' => 200, 'description_en' => 300, 'description_ar' => 300] as $col => $max) {
                $vals[$col] = mb_substr(trim($_POST['seo'][$slug][$col] ?? ''), 0, $max);
            }
            $stmt->execute([':slug' => $slug, ':ten' => $vals['title_en'], ':tar' => $vals['title_ar'], ':den' => $vals['description_en'], ':dar' => $vals['description_ar'],
                            ':ten2' => $vals['title_en'], ':tar2' => $vals['title_ar'], ':den2' => $vals['description_en'], ':dar2' => $vals['description_ar']]);
        }
        $svc = $pdo->prepare("UPDATE services SET meta_title_en = :ten, meta_title_ar = :tar, meta_desc_en = :den, meta_desc_ar = :dar WHERE id = :id");
        foreach ($_POST['svc'] ?? [] as $id => $row) {
            $svc->execute([
                ':ten' => mb_substr(trim($row['meta_title_en'] ?? ''), 0, 200), ':tar' => mb_substr(trim($row['meta_title_ar'] ?? ''), 0, 200),
                ':den' => mb_substr(trim($row['meta_desc_en'] ?? ''), 0, 300), ':dar' => mb_substr(trim($row['meta_desc_ar'] ?? ''), 0, 300),
                ':id' => (int)$id,
            ]);
        }
        flash('Google titles & descriptions saved.');
        header('Location: ' . SITE_URL . '/admin/settings#pageseo');
        exit;
    } elseif ($action === 'change_password') {
        $new_pass = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');
        $current = (string)($_POST['current_password'] ?? '');
        $row = $pdo->prepare("SELECT password FROM admins WHERE id = :id");
        $row->execute([':id' => (int)($_SESSION['admin_id'] ?? 0)]);
        $hash = $row->fetchColumn();
        if (!$hash || !password_verify($current, $hash)) {
            $errors[] = 'Your current password is not correct.';
        } elseif (strlen($new_pass) < 10) {
            $errors[] = 'New password must be at least 10 characters.';
        } elseif ($new_pass !== $confirm) {
            $errors[] = 'The two new passwords do not match.';
        } else {
            $pdo->prepare("UPDATE admins SET password = :p WHERE id = :id")
                ->execute([':p' => password_hash($new_pass, PASSWORD_DEFAULT), ':id' => (int)$_SESSION['admin_id']]);
            session_regenerate_id(true);
            flash('Password changed.');
            header('Location: ' . SITE_URL . '/admin/settings#account');
            exit;
        }
    }
}

$pages_seo = [];
foreach ($pdo->query("SELECT * FROM pages_seo") as $r) {
    $pages_seo[$r['page_slug']] = $r;
}
$services = $pdo->query("SELECT * FROM services ORDER BY sort_order")->fetchAll();
$mail_ok = mailEnabled();

// ---- System check ----
$sys = [];
$sys['php'] = PHP_VERSION;
try { $sys['db'] = $pdo->query("SELECT VERSION()")->fetchColumn(); } catch (Throwable $e) { $sys['db'] = '?'; }
try { $sys['conn'] = implode(' / ', $pdo->query("SELECT @@character_set_client, @@character_set_connection, @@character_set_results")->fetch(PDO::FETCH_NUM)); } catch (Throwable $e) { $sys['conn'] = '?'; }
try {
    $sys['bad_tables'] = $pdo->query("SELECT CONCAT(TABLE_NAME, '.', COLUMN_NAME, ' (', CHARACTER_SET_NAME, ')') FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND CHARACTER_SET_NAME IS NOT NULL AND CHARACTER_SET_NAME NOT IN ('utf8mb4','utf8','utf8mb3')
        AND TABLE_NAME IN ('" . implode("','", siteTables()) . "') LIMIT 12")->fetchAll(PDO::FETCH_COLUMN);
} catch (Throwable $e) { $sys['bad_tables'] = ['could not read: ' . $e->getMessage()]; }
$sys['arabic_ok'] = arabicSelfTest($pdo);
$sys['damaged'] = countDamagedArabic($pdo);
$sys['repair'] = json_decode((string)opt('_arabic_repair'), true) ?: null;
$sys['migration'] = json_decode((string)opt('_migration_log'), true) ?: null;
$mc = mailConfig();
$sys['mail'] = $mc;
$sys['port'] = null;
if (!empty($_GET['check_mail']) && $mc['transport'] === 'smtp' && $mc['host'] !== '') {
    $errno = 0; $errstr = '';
    $target = ($mc['secure'] === 'ssl' ? 'ssl://' : '') . $mc['host'];
    $fp = @fsockopen($target, $mc['port'] ?: 587, $errno, $errstr, 5);
    $sys['port'] = $fp ? 'open' : ('blocked — ' . ($errstr ?: 'error ' . $errno));
    if ($fp) fclose($fp);
}
$sys_problem = !$sys['arabic_ok'];

/**
 * Value to show in a field: what was just submitted (if there was an error) or what is saved
 */
function fieldValue($key, $language = 'en') {
    global $errors;
    if ($errors && isset($_POST['section'])) {
        $posted = $_POST[$key . '_' . $language] ?? $_POST[$key] ?? null;
        if ($posted !== null) return $posted;
    }
    return setting($key, $language);
}

adminHead('Settings', 'settings');
?>
<h1 class="a-title">⚙️ Settings</h1>
<p class="a-sub">Everything about your business lives here. Changes appear on the website immediately — no code editing needed.</p>

<?php adminFlash(); ?>
<?php if ($sys_problem): ?>
    <div class="toast warning">⚠️ Arabic text problem detected (<?php echo (int)$sys['damaged']; ?> value(s) with question marks<?php echo $sys['arabic_ok'] ? '' : ', database cannot save Arabic yet'; ?>). <a href="#system">Open System check</a> and press "Repair Arabic now".</div>
<?php endif; ?>
<?php if ($errors): ?>
    <div class="toast error">Not saved: <?php echo e(implode(' · ', $errors)); ?></div>
<?php endif; ?>
<?php if ($test_result && !$test_result['ok']): ?>
    <div class="toast error">
        Test email failed: <?php echo e($test_result['error']); ?>
        <?php if ($hint = mailErrorHint($test_result['error'])): ?>
            <br><br><strong>What to do:</strong> <?php echo e($hint); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="settings-nav card" style="padding: 12px 14px;">
    <?php foreach ($schema as $sk => $sec): ?>
        <a href="#<?php echo $sk; ?>" class="btn btn-gray btn-sm"><?php echo $sec['icon'] . ' ' . e($sec['title']); ?></a>
    <?php endforeach; ?>
    <a href="#pageseo" class="btn btn-gray btn-sm">🔎 Google Titles</a>
    <a href="#account" class="btn btn-gray btn-sm">🔒 Password</a>
    <a href="#system" class="btn btn-gray btn-sm">🩺 System check</a>
</div>

<?php foreach ($schema as $sk => $sec): ?>
<form method="POST" action="<?php echo SITE_URL; ?>/admin/settings#<?php echo $sk; ?>" class="card" id="<?php echo $sk; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="section" value="<?php echo e($sk); ?>">
    <h2><?php echo $sec['icon'] . ' ' . e($sec['title']); ?></h2>
    <?php if ($sk === 'email'): ?>
        <p class="hint">
            Status: <?php echo $mail_ok ? '<span class="badge b-sent">Email is set up</span>' : '<span class="badge b-failed">Not set up — enter the SMTP username and password</span>'; ?>
            &nbsp; Customers who reply to your emails reach <strong><?php echo e(EMAIL); ?></strong> (your business email).
        </p>
        <?php $from_file = array_filter(array_keys(mailConfigConstants()), fn($k) => mailFromConfigFile($k) !== null); ?>
        <?php if ($from_file): ?>
        <div class="toast" style="background: rgba(56,189,248,.1); border-color: #0284c7; color: #7dd3fc;">
            🔒 <?php echo count($from_file); ?> email setting(s) come from <strong><?php echo e(PRIVATE_CONFIG_FILE); ?></strong> on the server and are shown locked below.
            To change them, edit that file in cPanel → File Manager.
        </div>
        <?php endif; ?>
    <?php elseif (!empty($sec['help'])): ?>
        <p class="hint"><?php echo e($sec['help']); ?></p>
    <?php else: ?>
        <p class="hint">&nbsp;</p>
    <?php endif; ?>

    <div class="grid2">
    <?php foreach ($sec['fields'] as $key => $f):
        $wide = !empty($f['bilingual']) || $f['type'] === 'textarea'; ?>
        <div class="fg" style="<?php echo $wide ? 'grid-column: 1 / -1;' : ''; ?>">
            <?php if (($locked_value = mailFromConfigFile($key)) !== null): ?>
                <label><?php echo e($f['label']); ?> <span class="badge b-contacted">🔒 config file</span></label>
                <input type="text" disabled value="<?php
                    if ($f['type'] === 'password') echo '•••••••• (set in ' . PRIVATE_CONFIG_FILE . ')';
                    elseif ($f['type'] === 'toggle') echo $locked_value === '1' ? 'On' : 'Off';
                    else echo e($locked_value); ?>">
                <div class="help">Set by <code><?php echo e(mailConfigConstants()[$key]); ?></code> in <?php echo e(PRIVATE_CONFIG_FILE); ?></div>
            </div>
            <?php continue; endif; ?>
            <?php if ($f['type'] === 'toggle'): ?>
                <label class="switch"><input type="checkbox" name="<?php echo e($key); ?>" value="1" <?php echo fieldValue($key) === '1' ? 'checked' : ''; ?>> <?php echo e($f['label']); ?></label>
            <?php elseif (!empty($f['bilingual'])): ?>
                <label><?php echo e($f['label']); ?></label>
                <div class="grid2">
                    <?php foreach (['en' => 'English', 'ar' => 'العربية'] as $lc => $ln): ?>
                    <div>
                        <div class="help" style="margin: 0 0 4px;"><?php echo $ln; ?></div>
                        <?php if ($f['type'] === 'textarea'): ?>
                            <textarea name="<?php echo e($key . '_' . $lc); ?>" rows="<?php echo (int)($f['rows'] ?? 3); ?>" <?php echo $lc === 'ar' ? 'dir="rtl"' : ''; ?>><?php echo e(fieldValue($key, $lc)); ?></textarea>
                        <?php else: ?>
                            <input type="text" name="<?php echo e($key . '_' . $lc); ?>" value="<?php echo e(fieldValue($key, $lc)); ?>" <?php echo $lc === 'ar' ? 'dir="rtl"' : ''; ?>>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <label for="f-<?php echo e($key); ?>"><?php echo e($f['label']); ?></label>
                <?php if ($f['type'] === 'textarea'): ?>
                    <textarea id="f-<?php echo e($key); ?>" name="<?php echo e($key); ?>" rows="<?php echo (int)($f['rows'] ?? 3); ?>"><?php echo e(fieldValue($key)); ?></textarea>
                <?php elseif ($f['type'] === 'select'): ?>
                    <select id="f-<?php echo e($key); ?>" name="<?php echo e($key); ?>">
                        <?php foreach ($f['options'] as $ov => $ol): ?>
                        <option value="<?php echo e($ov); ?>" <?php echo fieldValue($key) === (string)$ov ? 'selected' : ''; ?>><?php echo e($ol); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($f['type'] === 'password'): ?>
                    <input id="f-<?php echo e($key); ?>" type="password" name="<?php echo e($key); ?>" autocomplete="new-password" placeholder="<?php echo opt($key) !== '' ? '•••••••• saved — type a new one to change' : 'Not set'; ?>">
                    <?php if (opt($key) !== ''): ?>
                        <label class="switch" style="margin-top: 8px; font-size: 13px;"><input type="checkbox" name="<?php echo e($key); ?>_clear" value="1"> Remove saved password</label>
                    <?php endif; ?>
                <?php else: ?>
                    <input id="f-<?php echo e($key); ?>" type="<?php echo $f['type'] === 'url' ? 'url' : ($f['type'] === 'email' ? 'email' : ($f['type'] === 'number' ? 'number' : ($f['type'] === 'time' ? 'time' : 'text'))); ?>"
                           name="<?php echo e($key); ?>" value="<?php echo e(fieldValue($key)); ?>" placeholder="<?php echo e($f['placeholder'] ?? ''); ?>">
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($f['help'])): ?><div class="help"><?php echo e($f['help']); ?></div><?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>

    <?php if ($sk === 'business' && SITE_URL_CONFIGURED === false && !IS_LOCAL): ?>
        <div class="toast warning" style="margin-bottom: 14px;">Tip: fill in "Website address" (e.g. https://<?php echo e(SITE_DOMAIN); ?>) so Google always sees one official address.</div>
    <?php endif; ?>
    <button type="submit" class="btn btn-orange">Save <?php echo e($sec['title']); ?></button>
</form>

<?php if ($sk === 'email'): ?>
<form method="POST" action="<?php echo SITE_URL; ?>/admin/settings#email" class="card">
    <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
    <input type="hidden" name="action" value="test_email">
    <h2>🧪 Send a test email</h2>
    <p class="hint">Save the SMTP settings above first, then send a test to make sure emails arrive.</p>
    <div class="row-actions">
        <input type="email" name="test_to" value="<?php echo e(mailConfig()['notify'][0] ?? EMAIL); ?>" style="max-width: 340px;" required>
        <button type="submit" class="btn btn-green">Send test email</button>
    </div>
</form>
<?php endif; ?>
<?php endforeach; ?>

<!-- Google titles & descriptions -->
<form method="POST" action="<?php echo SITE_URL; ?>/admin/settings#pageseo" class="card" id="pageseo">
    <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
    <input type="hidden" name="action" value="page_seo">
    <h2>🔎 Google titles & descriptions</h2>
    <p class="hint">This is the blue headline and the grey text people see in Google results. Best length: title up to <strong>60</strong> characters, description <strong>120–160</strong>. Put the service and city first. You can write <code>{phone}</code> and <code>{site_name}</code> — they are filled in from your settings.</p>

    <?php
    $page_names = ['home' => 'Home page', 'about' => 'About page', 'contact' => 'Contact page', 'privacy-policy' => 'Privacy Policy', 'terms' => 'Terms of Service'];
    foreach ($page_names as $slug => $pname):
        $row = $pages_seo[$slug] ?? []; ?>
    <details class="seo-item" <?php echo $slug === 'home' ? 'open' : ''; ?>>
        <summary><?php echo e($pname); ?></summary>
        <div class="grid2" style="margin-top: 12px;">
            <?php foreach (['en' => 'English', 'ar' => 'العربية'] as $lc => $ln): ?>
            <div>
                <div class="fg"><label>Title (<?php echo $ln; ?>)</label>
                    <input type="text" class="count" data-max="60" name="seo[<?php echo e($slug); ?>][title_<?php echo $lc; ?>]" value="<?php echo e($row['title_' . $lc] ?? ''); ?>" <?php echo $lc === 'ar' ? 'dir="rtl"' : ''; ?> placeholder="<?php echo e(__('seo_title_' . $slug, '')); ?>"></div>
                <div class="fg"><label>Description (<?php echo $ln; ?>)</label>
                    <textarea class="count" data-max="160" rows="3" name="seo[<?php echo e($slug); ?>][description_<?php echo $lc; ?>]" <?php echo $lc === 'ar' ? 'dir="rtl"' : ''; ?>><?php echo e($row['description_' . $lc] ?? ''); ?></textarea></div>
            </div>
            <?php endforeach; ?>
        </div>
    </details>
    <?php endforeach; ?>

    <?php foreach ($services as $svc): ?>
    <details class="seo-item">
        <summary>Service: <?php echo e($svc['name_en']); ?></summary>
        <div class="grid2" style="margin-top: 12px;">
            <?php foreach (['en' => 'English', 'ar' => 'العربية'] as $lc => $ln): ?>
            <div>
                <div class="fg"><label>Title (<?php echo $ln; ?>)</label>
                    <input type="text" class="count" data-max="60" name="svc[<?php echo (int)$svc['id']; ?>][meta_title_<?php echo $lc; ?>]" value="<?php echo e($svc['meta_title_' . $lc]); ?>" <?php echo $lc === 'ar' ? 'dir="rtl"' : ''; ?>></div>
                <div class="fg"><label>Description (<?php echo $ln; ?>)</label>
                    <textarea class="count" data-max="160" rows="3" name="svc[<?php echo (int)$svc['id']; ?>][meta_desc_<?php echo $lc; ?>]" <?php echo $lc === 'ar' ? 'dir="rtl"' : ''; ?>><?php echo e($svc['meta_desc_' . $lc]); ?></textarea></div>
            </div>
            <?php endforeach; ?>
        </div>
    </details>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-orange" style="margin-top: 14px;">Save Google titles</button>
</form>

<!-- System check -->
<div class="card" id="system">
    <h2>🩺 System check</h2>
    <p class="hint">If something doesn't work, take a screenshot of this box and send it to your developer.</p>
    <table class="kv-sys">
        <tr><th>Running on</th><td><?php echo PHP_OS_FAMILY === 'Windows' ? 'Your Windows computer (XAMPP/WAMP) — "localhost" and Server mail cannot send email here; use Gmail SMTP to test' : (IS_LOCAL ? 'Local test server (' . e(PHP_OS_FAMILY) . ')' : 'Live server: ' . e(SITE_DOMAIN) . ' (' . e(PHP_OS_FAMILY) . ')'); ?></td></tr>
        <tr><th>PHP / Database</th><td><?php echo e($sys['php']); ?> / <?php echo e($sys['db']); ?></td></tr>
        <tr><th>Database connection charset</th><td><?php echo e($sys['conn']); ?> <?php echo str_contains($sys['conn'], 'utf8') ? '<span class="badge b-sent">OK</span>' : '<span class="badge b-failed">Problem</span>'; ?></td></tr>
        <tr><th>Tables that cannot hold Arabic</th><td><?php echo $sys['bad_tables'] ? e(implode(', ', $sys['bad_tables'])) . ' <span class="badge b-failed">Problem</span>' : 'None <span class="badge b-sent">OK</span>'; ?></td></tr>
        <tr><th>Arabic save test</th><td><?php echo $sys['arabic_ok'] ? 'Arabic is saved correctly <span class="badge b-sent">OK</span>' : 'Arabic turns into ???? <span class="badge b-failed">Problem</span>'; ?></td></tr>
        <tr><th>Arabic values with ????</th><td><?php echo (int)$sys['damaged']; ?> <?php echo $sys['damaged'] ? '<span class="badge b-failed">Problem</span>' : '<span class="badge b-sent">OK</span>'; ?></td></tr>
        <?php if ($sys['repair']): ?>
        <tr><th>Last Arabic repair</th><td><?php echo e($sys['repair']['at'] ?? ''); ?> — restored <?php echo (int)($sys['repair']['restored'] ?? 0); ?>
            <?php foreach ($sys['repair']['errors'] ?? [] as $er): ?><div style="color:#f87171;font-size:12px;"><?php echo e($er); ?></div><?php endforeach; ?>
            <?php if (str_contains(implode(' ', $sys['repair']['errors'] ?? []), 'ALTER command denied')): ?>
                <div class="help" style="color:#fcd34d;">Fix: cPanel → <strong>MySQL Databases</strong> → "Add User To Database" (or click your user under "Privileged Users") → tick <strong>ALL PRIVILEGES</strong> → Make Changes. Then press "Repair Arabic now".</div>
            <?php endif; ?></td></tr>
        <?php endif; ?>
        <?php if (!empty($sys['migration']['failed'])): ?>
        <tr><th>Upgrade steps that failed</th><td><?php foreach ($sys['migration']['failed'] as $step => $msg): ?><div style="color:#f87171;font-size:12px;"><?php echo e($step . ': ' . $msg); ?></div><?php endforeach; ?></td></tr>
        <?php endif; ?>
        <tr><th>Email sending</th><td><?php echo $sys['mail']['transport'] === 'phpmail' ? 'Server mail (PHP mail)' . (function_exists('mail') ? ' <span class="badge b-sent">available</span>' : ' <span class="badge b-failed">disabled on this server</span>') : 'SMTP ' . e($sys['mail']['host'] . ':' . $sys['mail']['port']); ?>
            <?php if ($sys['port'] === null && $sys['mail']['transport'] === 'smtp'): ?> — <a href="<?php echo SITE_URL; ?>/admin/settings?check_mail=1#system">check connection</a><?php endif; ?>
            <?php if ($sys['port'] !== null): ?> — connection <?php echo $sys['port'] === 'open' ? '<span class="badge b-sent">open</span>' : '<span class="badge b-failed">' . e($sys['port']) . '</span><div class="help">Your hosting blocks this server. Choose "Server mail" in Email (SMTP), or use your cPanel email account with server "localhost".</div>'; ?><?php endif; ?></td></tr>
        <tr><th>Settings file</th><td><?php echo PRIVATE_CONFIG_EXISTS ? e(PRIVATE_CONFIG_FILE) . ' <span class="badge b-sent">found</span>' : 'config/config.php <span class="badge b-failed">missing</span>'; ?></td></tr>
    </table>
    <div class="row-actions" style="margin-top: 14px;">
        <form method="POST" action="<?php echo SITE_URL; ?>/admin/settings#system" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
            <input type="hidden" name="action" value="repair_arabic">
            <button type="submit" class="btn btn-orange">Repair Arabic now</button>
        </form>
        <a href="<?php echo SITE_URL; ?>/admin/settings#system" class="btn btn-gray">Check again</a>
    </div>
</div>
<style>
    .kv-sys th { width: 34%; text-transform: none; letter-spacing: 0; font-size: 13px; color: #94a3b8; font-weight: 600; vertical-align: top; }
    .kv-sys td { font-size: 14px; }
</style>

<!-- Password -->
<form method="POST" action="<?php echo SITE_URL; ?>/admin/settings#account" class="card" id="account">
    <input type="hidden" name="csrf_token" value="<?php echo e($csrf_token); ?>">
    <input type="hidden" name="action" value="change_password">
    <h2>🔒 Admin password</h2>
    <p class="hint">Use at least 10 characters. Don't reuse your email password.</p>
    <div class="grid3">
        <div class="fg"><label>Current password</label><input type="password" name="current_password" autocomplete="current-password" required></div>
        <div class="fg"><label>New password</label><input type="password" name="new_password" autocomplete="new-password" minlength="10" required></div>
        <div class="fg"><label>Confirm new password</label><input type="password" name="confirm_password" autocomplete="new-password" minlength="10" required></div>
    </div>
    <button type="submit" class="btn btn-orange">Change password</button>
</form>

<style>
    .settings-nav { display: flex; flex-wrap: wrap; gap: 8px; position: sticky; top: 70px; z-index: 10; }
    .seo-item { border: 1px solid #233044; border-radius: 10px; padding: 12px 14px; margin-bottom: 10px; background: #0f1623; }
    .seo-item summary { cursor: pointer; font-weight: 700; font-size: 14px; }
    .counter { font-size: 11px; margin-top: 4px; color: #64748b; }
    .counter.over { color: #f87171; font-weight: 700; }
    @media (max-width: 760px) { .settings-nav { position: static; } }
</style>
<script>
// Live character counters for Google titles/descriptions
document.querySelectorAll('.count').forEach(function (el) {
    var c = document.createElement('div'); c.className = 'counter'; el.after(c);
    var max = parseInt(el.dataset.max, 10);
    function upd() { var n = el.value.length; c.textContent = n + ' / ' + max + ' characters'; c.classList.toggle('over', n > max); }
    el.addEventListener('input', upd); upd();
});
</script>
<?php adminFoot(); ?>
