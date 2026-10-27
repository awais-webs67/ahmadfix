<?php
/**
 * Ahmad Fix — Email system
 *
 * - New lead  → branded alert to you (Admin → Settings → Email → "Send new-lead alerts to")
 * - New lead  → automatic "Thank you, we received your request" to the customer (if they gave an email)
 * - Admin     → send / reply to customers from the admin panel
 * Every email is saved in the email_log table (Admin → Emails).
 *
 * SMTP server, login and all texts are set in Admin → Settings (nothing is hard-coded here).
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

require_once SITE_ROOT . '/includes/PHPMailer/Exception.php';
require_once SITE_ROOT . '/includes/PHPMailer/PHPMailer.php';
require_once SITE_ROOT . '/includes/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

// ============================================
// Settings
// ============================================

/**
 * SMTP settings from Admin → Settings → Email (config.local.php values are a fallback)
 */
function mailConfig() {
    $user = opt('smtp_user', defined('SMTP_USER') ? (string)SMTP_USER : '');
    $pass = decryptSecret(opt('smtp_pass'));
    if ($pass === '' && defined('SMTP_PASS')) $pass = (string)SMTP_PASS;
    $from = opt('smtp_from_email');
    $notify = array_values(array_filter(array_map('trim', preg_split('/[,;\s]+/', opt('notify_emails', EMAIL))), fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
    return [
        'host'       => opt('smtp_host', defined('SMTP_HOST') ? (string)SMTP_HOST : 'smtp.gmail.com'),
        'port'       => (int)opt('smtp_port', defined('SMTP_PORT') ? (string)SMTP_PORT : '587'),
        'secure'     => opt('smtp_secure', 'tls'),
        'user'       => $user,
        'pass'       => $pass,
        'from_email' => filter_var($from, FILTER_VALIDATE_EMAIL) ? $from : ($user ?: EMAIL),
        'from_name'  => opt('smtp_from_name', SITE_NAME),
        'notify'     => $notify ?: [EMAIL],
        'auto_reply' => opt('auto_reply_enabled', '1') === '1',
    ];
}

function mailEnabled() {
    $c = mailConfig();
    return $c['host'] !== '' && $c['user'] !== '' && $c['pass'] !== '';
}

/**
 * Text from a language file, in a specific language (emails are written in the customer's language)
 */
function langText($key, $language) {
    static $files = [];
    if (!isset($files[$language])) {
        $file = SITE_ROOT . '/lang/' . ($language === 'ar' ? 'ar' : 'en') . '.php';
        $files[$language] = is_file($file) ? require $file : [];
    }
    return $files[$language][$key] ?? $key;
}

// ============================================
// Formatting helpers
// ============================================

/**
 * Saudi phone in any format → number for wa.me (e.g. 0512345678 → 966512345678)
 */
function customerWhatsApp($phone) {
    $d = preg_replace('/[^0-9]/', '', (string)$phone);
    if (str_starts_with($d, '00')) return substr($d, 2);
    if (str_starts_with($d, '0')) return '966' . substr($d, 1);
    if (strlen($d) === 9 && $d[0] === '5') return '966' . $d;
    return $d;
}

function emailDate($ymd, $language) {
    if (!$ymd || !($d = DateTime::createFromFormat('Y-m-d', substr($ymd, 0, 10)))) return '';
    if ($language === 'ar') {
        $days = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        return $days[(int)$d->format('w')] . ' ' . $d->format('j') . ' ' . $months[(int)$d->format('n') - 1] . ' ' . $d->format('Y');
    }
    return $d->format('l, j F Y');
}

function timeSlotLabel($slot, $language) {
    return $slot ? langText('time_' . $slot, $language) : '';
}

function cityLabel($city_en, $language) {
    if (!$city_en) return '';
    foreach (getAreas() as $a) {
        if ($a['name_en'] === $city_en) return $a['name_' . $language];
    }
    return $city_en;
}

/**
 * Business placeholders in one language (Arabic name, phone kept left-to-right inside Arabic text)
 */
function langVars($language) {
    $ltr = fn($v) => $language === 'ar' ? "\u{2066}" . $v . "\u{2069}" : $v;
    return [
        'site_name' => $language === 'ar' ? SITE_NAME_AR : SITE_NAME,
        'phone'     => $ltr(PHONE),
        'whatsapp'  => $ltr(WHATSAPP),
    ];
}

/**
 * Placeholder values for one lead, in one language
 */
function leadVars(array $lead, $language) {
    $plain = fn($v) => html_entity_decode((string)$v, ENT_QUOTES, 'UTF-8');
    return langVars($language) + [
        'name'    => $plain($lead['customer_name'] ?? ''),
        'service' => serviceLabel($lead['service_requested'] ?? '', $language),
        'city'    => cityLabel($lead['city'] ?? '', $language),
        'date'    => emailDate($lead['preferred_date'] ?? '', $language) ?: ($language === 'ar' ? 'سيتم تحديده' : 'to be confirmed'),
        'time'    => timeSlotLabel($lead['preferred_time'] ?? '', $language) ?: ($language === 'ar' ? 'سيتم تحديده' : 'to be confirmed'),
    ];
}

/**
 * Plain text → safe HTML paragraphs
 */
function textToHtml($text) {
    $text = trim(str_replace(["\r\n", "\r"], "\n", (string)$text));
    $paras = preg_split("/\n{2,}/", $text);
    $out = '';
    foreach ($paras as $p) {
        $out .= '<p style="margin:0 0 14px;">' . nl2br(htmlspecialchars($p, ENT_QUOTES, 'UTF-8')) . '</p>';
    }
    return $out;
}

/**
 * Details table (service, city, date …) used in the emails
 */
function leadDetailsTable(array $lead, $language, $for_admin = false) {
    $L = fn($en, $ar) => $language === 'ar' ? $ar : $en;
    $plain = fn($v) => htmlspecialchars(html_entity_decode((string)$v, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
    $rows = [];
    if ($for_admin) {
        $rows[] = [$L('Customer', 'العميل'), '<strong>' . $plain($lead['customer_name']) . '</strong>'];
        $tel = preg_replace('/[^+0-9]/', '', $lead['phone']);
        $rows[] = [$L('Phone', 'الجوال'), '<a href="tel:' . $tel . '" style="color:#E56D00;font-weight:700;text-decoration:none;">' . $plain($lead['phone']) . '</a>'];
        if (!empty($lead['email'])) {
            $rows[] = [$L('Email', 'البريد'), '<a href="mailto:' . $plain($lead['email']) . '" style="color:#E56D00;text-decoration:none;">' . $plain($lead['email']) . '</a>'];
        }
    }
    $rows[] = [$L('Service', 'الخدمة'), $plain(serviceLabel($lead['service_requested'] ?? '', $language))];
    if (!empty($lead['city']))           $rows[] = [$L('City', 'المدينة'), $plain(cityLabel($lead['city'], $language))];
    if (!empty($lead['address']))        $rows[] = [$L('Address / district', 'العنوان / الحي'), $plain($lead['address'])];
    if (!empty($lead['preferred_date'])) $rows[] = [$L('Preferred date', 'التاريخ المفضل'), $plain(emailDate($lead['preferred_date'], $language))];
    if (!empty($lead['preferred_time'])) $rows[] = [$L('Preferred time', 'الوقت المفضل'), $plain(timeSlotLabel($lead['preferred_time'], $language))];
    if (!empty($lead['message']))        $rows[] = [$L('Message', 'الرسالة'), nl2br($plain($lead['message']))];
    if ($for_admin) {
        $rows[] = [$L('Language', 'اللغة'), ($lead['lang'] ?? 'en') === 'ar' ? 'العربية (Arabic)' : 'English'];
        $rows[] = [$L('Received', 'وقت الاستلام'), $plain(date('D, j M Y — g:i A', strtotime($lead['submitted_at'] ?? 'now')))];
        $rows[] = [$L('Lead #', 'رقم الطلب'), '#' . (int)$lead['id']];
    }
    $align = $language === 'ar' ? 'right' : 'left';
    $html = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#FAFAFA;border:1px solid #EEEEEE;border-radius:10px;margin:6px 0 20px;">';
    foreach ($rows as $i => [$k, $v]) {
        $border = $i ? 'border-top:1px solid #EEEEEE;' : '';
        $html .= '<tr><td style="' . $border . 'padding:11px 16px;width:36%;font-size:13px;color:#777777;vertical-align:top;text-align:' . $align . ';">' . $k . '</td>'
               . '<td style="' . $border . 'padding:11px 16px;font-size:14px;color:#1A1A1A;vertical-align:top;text-align:' . $align . ';line-height:1.55;">' . $v . '</td></tr>';
    }
    return $html . '</table>';
}

/**
 * Button that works in every email app
 */
function emailButton($label, $url, $bg = '#FF7A00', $color = '#FFFFFF') {
    return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" target="_blank" style="display:inline-block;background:' . $bg . ';color:' . $color
        . ';font-size:14px;font-weight:700;text-decoration:none;padding:12px 22px;border-radius:8px;margin:4px 4px 4px 0;">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';
}

// ============================================
// Branded layout
// ============================================

/**
 * Full HTML email around $body_html.
 * $o = [lang, title, preheader, body_html, buttons_html, badge]
 */
function emailLayout(array $o) {
    $language = ($o['lang'] ?? 'en') === 'ar' ? 'ar' : 'en';
    $dir = $language === 'ar' ? 'rtl' : 'ltr';
    $align = $language === 'ar' ? 'right' : 'left';
    $name = $language === 'ar' ? SITE_NAME_AR : SITE_NAME;
    $font = $language === 'ar' ? "Tahoma,'Segoe UI',Arial,sans-serif" : "'Segoe UI',Roboto,Helvetica,Arial,sans-serif";
    $e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    $tel = preg_replace('/[^+0-9]/', '', PHONE);
    $wa = ltrim(preg_replace('/[^+0-9]/', '', WHATSAPP), '+');
    $site = SITE_URL . '/' . $language;
    $badge = !empty($o['badge']) ? '<div style="display:inline-block;background:#FFF1E5;color:#C2410C;font-size:12px;font-weight:700;padding:5px 12px;border-radius:999px;margin-bottom:14px;">' . $e($o['badge']) . '</div>' : '';
    $socials = '';
    foreach (socialLinks() as $net => $url) {
        $socials .= '<a href="' . $e($url) . '" style="color:#BBBBBB;text-decoration:none;margin:0 6px;font-size:12px;">' . $e(ucfirst($net)) . '</a>';
    }

    return '<!DOCTYPE html><html lang="' . $language . '" dir="' . $dir . '"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<meta name="color-scheme" content="light"><title>' . $e($o['title'] ?? $name) . '</title></head>'
        . '<body style="margin:0;padding:0;background:#F2F2F2;font-family:' . $font . ';">'
        . '<div style="display:none;max-height:0;overflow:hidden;opacity:0;">' . $e($o['preheader'] ?? '') . '</div>'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F2F2F2;"><tr><td align="center" style="padding:24px 12px;">'
        . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#FFFFFF;border-radius:14px;overflow:hidden;border:1px solid #E5E5E5;">'
        // Orange top line + logo
        . '<tr><td style="height:5px;background:#FF7A00;font-size:0;line-height:0;">&nbsp;</td></tr>'
        . '<tr><td align="center" style="padding:22px 24px 8px;"><a href="' . $e($site) . '" target="_blank"><img src="cid:logo" alt="' . $e($name) . '" width="150" style="display:block;width:150px;max-width:60%;height:auto;border:0;"></a></td></tr>'
        // Body
        . '<tr><td dir="' . $dir . '" style="padding:12px 32px 8px;text-align:' . $align . ';color:#333333;font-size:15px;line-height:1.65;">'
        . $badge
        . (!empty($o['title']) ? '<h1 style="margin:0 0 14px;font-size:22px;line-height:1.3;color:#1A1A1A;font-weight:800;">' . $e($o['title']) . '</h1>' : '')
        . ($o['body_html'] ?? '')
        . (!empty($o['buttons_html']) ? '<div style="margin:10px 0 18px;">' . $o['buttons_html'] . '</div>' : '')
        . '</td></tr>'
        // Footer
        . '<tr><td dir="' . $dir . '" style="background:#0D0D0D;padding:22px 32px;text-align:center;color:#BBBBBB;font-size:12px;line-height:1.8;">'
        . '<div style="color:#FFFFFF;font-size:14px;font-weight:700;margin-bottom:4px;">' . $e($name) . '</div>'
        . '<a href="tel:' . $tel . '" style="color:#FF9A33;text-decoration:none;" dir="ltr">' . $e(PHONE) . '</a> &nbsp;·&nbsp; '
        . '<a href="https://wa.me/' . $wa . '" style="color:#FF9A33;text-decoration:none;">WhatsApp</a> &nbsp;·&nbsp; '
        . '<a href="' . $e($site) . '" style="color:#FF9A33;text-decoration:none;">' . $e(SITE_DOMAIN) . '</a><br>'
        . $e(trim(setting('address_en', $language)))
        . ($socials ? '<div style="margin-top:8px;">' . $socials . '</div>' : '')
        . '</td></tr>'
        . '</table></td></tr></table></body></html>';
}

/**
 * Signature text (Admin → Settings → Email Texts)
 */
function emailSignature($language) {
    return fillTemplate(setting('email_signature', $language), langVars($language));
}

// ============================================
// Sending + log
// ============================================

function logEmail(array $row) {
    global $pdo;
    try {
        $pdo->prepare("INSERT INTO email_log (lead_id, type, to_email, to_name, subject, message, status, error, sent_by)
                       VALUES (:lead, :type, :to, :name, :subject, :message, :status, :error, :by)")
            ->execute([
                ':lead' => $row['lead_id'] ?? null,
                ':type' => $row['type'] ?? 'compose',
                ':to' => mb_substr($row['to'] ?? '', 0, 190),
                ':name' => mb_substr($row['to_name'] ?? '', 0, 100),
                ':subject' => mb_substr($row['subject'] ?? '', 0, 255),
                ':message' => $row['message'] ?? null,
                ':status' => $row['status'] ?? 'sent',
                ':error' => isset($row['error']) ? mb_substr($row['error'], 0, 500) : null,
                ':by' => $row['sent_by'] ?? null,
            ]);
    } catch (Throwable $e) {
        error_log('Email log failed: ' . $e->getMessage());
    }
}

/**
 * Send one email through the SMTP server in Admin → Settings.
 * $o = [to, to_name, subject, html, text, reply_to, reply_to_name, lead_id, type, log_message, sent_by]
 * Returns ['ok' => bool, 'error' => string]
 */
function sendEmail(array $o) {
    $c = mailConfig();
    $log = [
        'lead_id' => $o['lead_id'] ?? null, 'type' => $o['type'] ?? 'compose', 'to' => $o['to'] ?? '',
        'to_name' => $o['to_name'] ?? '', 'subject' => $o['subject'] ?? '', 'message' => $o['log_message'] ?? null,
        'sent_by' => $o['sent_by'] ?? null,
    ];

    if (!filter_var($o['to'] ?? '', FILTER_VALIDATE_EMAIL)) {
        logEmail($log + ['status' => 'failed', 'error' => 'Invalid recipient address']);
        return ['ok' => false, 'error' => 'Invalid recipient address.'];
    }
    if (!mailEnabled()) {
        logEmail($log + ['status' => 'failed', 'error' => 'SMTP not configured']);
        return ['ok' => false, 'error' => 'Email is not set up yet. Add your SMTP login in Admin → Settings → Email.'];
    }

    try {
        $m = new PHPMailer(true);
        $m->isSMTP();
        $m->Host = $c['host'];
        $m->Port = $c['port'] ?: 587;
        $m->SMTPAuth = true;
        $m->Username = $c['user'];
        $m->Password = $c['pass'];
        if ($c['secure'] === 'ssl') {
            $m->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($c['secure'] === 'none') {
            $m->SMTPSecure = '';
            $m->SMTPAutoTLS = false;
        } else {
            $m->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $m->Timeout = 20;
        $m->CharSet = 'UTF-8';
        $m->Encoding = 'base64';
        $m->setFrom($c['from_email'], $c['from_name']);
        $m->addAddress($o['to'], $o['to_name'] ?? '');
        if (!empty($o['reply_to']) && filter_var($o['reply_to'], FILTER_VALIDATE_EMAIL)) {
            $m->addReplyTo($o['reply_to'], $o['reply_to_name'] ?? '');
        }
        foreach ($o['bcc'] ?? [] as $bcc) {
            if (filter_var($bcc, FILTER_VALIDATE_EMAIL)) $m->addBCC($bcc);
        }
        $logo = SITE_ROOT . '/assets/images/logo-header.png';
        if (is_file($logo)) {
            $m->addEmbeddedImage($logo, 'logo', 'logo.png');
        }
        $m->isHTML(true);
        $m->Subject = $o['subject'];
        $m->Body = $o['html'];
        $m->AltBody = $o['text'] ?? trim(html_entity_decode(strip_tags(str_replace(['<br>', '</p>', '</tr>'], "\n", $o['html'])), ENT_QUOTES, 'UTF-8'));
        $m->send();
        logEmail($log + ['status' => 'sent']);
        return ['ok' => true, 'error' => ''];
    } catch (Throwable $e) {
        $err = isset($m) && $m->ErrorInfo ? $m->ErrorInfo : $e->getMessage();
        error_log('Email send failed to ' . $o['to'] . ': ' . $err);
        logEmail($log + ['status' => 'failed', 'error' => $err]);
        return ['ok' => false, 'error' => $err];
    }
}

// ============================================
// The emails
// ============================================

/**
 * New lead → alert to you
 */
function sendLeadNotification(array $lead) {
    $c = mailConfig();
    $v = leadVars($lead, 'en');
    $tel = preg_replace('/[^+0-9]/', '', $lead['phone']);
    $wa_num = customerWhatsApp($lead['phone']);
    $is_booking = !empty($lead['preferred_date']) || !empty($lead['city']);
    $title = ($is_booking ? 'New booking request' : 'New contact message') . ' — ' . $v['name'];

    $body = '<p style="margin:0 0 14px;">A customer just ' . ($is_booking ? 'requested a technician' : 'sent a message') . ' on your website. Call them back quickly — fast replies win more jobs.</p>'
          . leadDetailsTable($lead, 'en', true);
    $buttons = emailButton('📞 Call ' . $v['name'], 'tel:' . $tel)
             . emailButton('💬 WhatsApp', 'https://wa.me/' . $wa_num, '#0E7A43')
             . emailButton('Open in admin panel', adminBaseUrl() . '/admin/lead?id=' . (int)$lead['id'], '#1A1A1A');

    $html = emailLayout(['lang' => 'en', 'title' => $title, 'preheader' => $v['service'] . ($v['city'] ? ' · ' . $v['city'] : '') . ' · ' . $lead['phone'],
                         'badge' => $is_booking ? 'BOOKING' : 'CONTACT', 'body_html' => $body, 'buttons_html' => $buttons]);
    $subject = ($is_booking ? '🔔 New booking: ' : '🔔 New message: ') . $v['name'] . ' — ' . $v['service'] . ($v['city'] ? ' (' . $v['city'] . ')' : '');

    $results = [];
    foreach ($c['notify'] as $to) {
        $results[] = sendEmail([
            'to' => $to, 'subject' => $subject, 'html' => $html, 'type' => 'notification', 'lead_id' => (int)$lead['id'],
            // Press "Reply" in your inbox to answer the customer directly
            'reply_to' => $lead['email'] ?? '', 'reply_to_name' => $v['name'],
            'log_message' => 'New lead alert',
        ]);
    }
    return $results;
}

/**
 * Website address for links in emails to YOU (admin links must use the live address if saved)
 */
function adminBaseUrl() {
    $saved = rtrim(opt('site_url'), '/');
    return ($saved && filter_var($saved, FILTER_VALIDATE_URL)) ? $saved : REQUEST_BASE_URL;
}

/**
 * New lead → automatic "Thank you" to the customer
 */
function sendCustomerAutoReply(array $lead) {
    $email = html_entity_decode($lead['email'] ?? '', ENT_QUOTES, 'UTF-8');
    if (!mailConfig()['auto_reply'] || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return null;
    }
    $language = ($lead['lang'] ?? 'en') === 'ar' ? 'ar' : 'en';
    $v = leadVars($lead, $language);
    $subject = fillTemplate(setting('auto_reply_subject', $language), $v);
    $message = fillTemplate(setting('auto_reply_message', $language), $v);
    $tel = preg_replace('/[^+0-9]/', '', PHONE);
    $wa = ltrim(preg_replace('/[^+0-9]/', '', WHATSAPP), '+');
    $ar = $language === 'ar';

    $body = textToHtml($message)
          . '<p style="margin:18px 0 6px;font-weight:700;color:#1A1A1A;">' . ($ar ? 'ملخص طلبك' : 'Your request') . '</p>'
          . leadDetailsTable($lead, $language, false)
          . textToHtml(emailSignature($language));
    $buttons = emailButton($ar ? 'اتصل بنا' : 'Call us', 'tel:' . $tel)
             . emailButton($ar ? 'واتساب' : 'WhatsApp', 'https://wa.me/' . $wa, '#0E7A43');

    $html = emailLayout(['lang' => $language, 'title' => $ar ? 'شكراً لتواصلك معنا!' : 'Thank you for contacting us!',
                         'preheader' => $subject, 'body_html' => $body, 'buttons_html' => $buttons]);

    return sendEmail([
        'to' => $email, 'to_name' => $v['name'], 'subject' => $subject, 'html' => $html,
        'reply_to' => EMAIL, 'reply_to_name' => $ar ? SITE_NAME_AR : SITE_NAME,
        'type' => 'auto_reply', 'lead_id' => (int)$lead['id'], 'log_message' => $message,
    ]);
}

/**
 * Admin panel → send / reply to a customer
 * $lead may be null for a new email to any address.
 */
function sendCustomerEmail($to, $to_name, $subject, $message, $language = 'en', ?array $lead = null, $sent_by = null, $include_request = true) {
    $language = $language === 'ar' ? 'ar' : 'en';
    $ar = $language === 'ar';
    $vars = $lead ? leadVars($lead, $language) : langVars($language) + ['name' => $to_name];
    $message = fillTemplate($message, $vars);
    $subject = fillTemplate($subject, $vars);

    $body = textToHtml($message) . textToHtml(emailSignature($language));
    if ($lead && $include_request) {
        $body .= '<p style="margin:18px 0 6px;font-size:13px;font-weight:700;color:#777777;">' . ($ar ? 'بخصوص طلبك' : 'About your request') . ' #' . (int)$lead['id'] . '</p>'
               . leadDetailsTable($lead, $language, false);
    }
    $tel = preg_replace('/[^+0-9]/', '', PHONE);
    $wa = ltrim(preg_replace('/[^+0-9]/', '', WHATSAPP), '+');
    $buttons = emailButton($ar ? 'اتصل بنا' : 'Call us', 'tel:' . $tel) . emailButton($ar ? 'واتساب' : 'WhatsApp', 'https://wa.me/' . $wa, '#0E7A43');
    $review = opt('google_review_url');
    if ($review && filter_var($review, FILTER_VALIDATE_URL) && $lead && ($lead['status'] ?? '') === 'completed') {
        $buttons .= emailButton($ar ? '⭐ قيّمنا على جوجل' : '⭐ Review us on Google', $review, '#1A1A1A');
    }

    $html = emailLayout(['lang' => $language, 'title' => '', 'preheader' => mb_substr(trim(preg_replace('/\s+/', ' ', $message)), 0, 120),
                         'body_html' => $body, 'buttons_html' => $buttons]);

    return sendEmail([
        'to' => $to, 'to_name' => $to_name, 'subject' => $subject, 'html' => $html,
        'reply_to' => EMAIL, 'reply_to_name' => $ar ? SITE_NAME_AR : SITE_NAME,
        'type' => $lead ? 'reply' : 'compose', 'lead_id' => $lead ? (int)$lead['id'] : null,
        'log_message' => $message, 'sent_by' => $sent_by,
    ]);
}

/**
 * Admin → Settings → "Send test email"
 */
function sendTestEmail($to) {
    $html = emailLayout(['lang' => 'en', 'title' => 'Your email is working ✅', 'badge' => 'TEST',
        'body_html' => '<p style="margin:0 0 14px;">This test was sent from your website\'s admin panel. New-lead alerts and automatic replies to customers will look like this and be sent from this server.</p>'
            . '<p style="margin:0 0 14px;color:#777777;font-size:13px;">Server: ' . htmlspecialchars(mailConfig()['host'], ENT_QUOTES, 'UTF-8') . ' · Sent: ' . date('D, j M Y g:i A') . '</p>']);
    return sendEmail(['to' => $to, 'subject' => '✅ Test email from ' . SITE_NAME, 'html' => $html, 'type' => 'test', 'log_message' => 'SMTP test']);
}

/**
 * Called by the contact form for every new lead
 */
function sendContactEmails(array $lead) {
    if (!mailEnabled()) {
        error_log('Email not configured (Admin → Settings → Email). Lead #' . (int)$lead['id'] . ' was saved without emails.');
        return false;
    }
    sendLeadNotification($lead);
    sendCustomerAutoReply($lead);
    return true;
}
