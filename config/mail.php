<?php
/**
 * Ahmad Fix — SMTP Mail Helper
 * Uses PHPMailer to send beautifully styled notification and auto-response emails
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

// Load PHPMailer core files
require_once SITE_ROOT . '/includes/PHPMailer/Exception.php';
require_once SITE_ROOT . '/includes/PHPMailer/PHPMailer.php';
require_once SITE_ROOT . '/includes/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send booking notification to Admin and confirmation auto-response to Customer
 * 
 * @param string $name Customer's Name
 * @param string $phone Customer's Phone
 * @param string $email Customer's Email (optional)
 * @param string $service Service requested key (e.g. ac, fridge, etc.)
 * @param string $message Detailed message
 * @param string $lang Language used for submission ('en' or 'ar')
 * @return bool True if emails triggered, false otherwise (errors are logged to PHP error log)
 */
function sendContactEmails($name, $phone, $email, $service, $message, $lang = 'en') {
    if (!defined('SMTP_ENABLED') || !SMTP_ENABLED) {
        error_log("SMTP mailing is disabled in config.php. Skipping email dispatch.");
        return false;
    }

    // Map service slugs/codes to human readable names in EN and AR
    $services_map = [
        'ac' => ['en' => 'AC Repair & Maintenance', 'ar' => 'صيانة وإصلاح المكيفات'],
        'fridge' => ['en' => 'Refrigerator Repair', 'ar' => 'صيانة وإصلاح الثلاجات'],
        'washer' => ['en' => 'Washing Machine Repair', 'ar' => 'صيانة وإصلاح الغسالات'],
        'oven' => ['en' => 'Oven & Stove Repair', 'ar' => 'صيانة وإصلاح الأفران والبوتاجاز'],
        'other' => ['en' => 'General Inquiry / Other', 'ar' => 'استفسار عام / خدمة أخرى']
    ];

    $service_name_en = $services_map[$service]['en'] ?? 'General Inquiry';
    $service_name_ar = $services_map[$service]['ar'] ?? 'استفسار عام';
    $service_display = ($lang === 'ar') ? $service_name_ar : $service_name_en;

    $clean_phone = preg_replace('/[^+0-9]/', '', $phone);
    $wa_link = "https://wa.me/" . ltrim($clean_phone, '+');
    $brand_name = defined('SITE_NAME') ? SITE_NAME : 'Ahmad Fix';
    // New-lead emails go to the business email set in the admin panel
    $admin_email = defined('EMAIL') ? EMAIL : (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'ahmadarian538@gmail.com');

    $logo_path = SITE_ROOT . '/assets/images/logo.png';

    // ============================================
    // 1. Send Admin Notification Email
    // ============================================
    try {
        $mailAdmin = new PHPMailer(true);

        // SMTP settings
        $mailAdmin->isSMTP();
        $mailAdmin->Host       = SMTP_HOST;
        $mailAdmin->SMTPAuth   = true;
        $mailAdmin->Username   = SMTP_USER;
        $mailAdmin->Password   = SMTP_PASS;
        $mailAdmin->SMTPSecure = (SMTP_SECURE === 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mailAdmin->Port       = SMTP_PORT;
        $mailAdmin->CharSet    = 'UTF-8';

        // Sender & Recipient
        $mailAdmin->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mailAdmin->addAddress($admin_email, $brand_name . ' Admin');
        
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mailAdmin->addReplyTo($email, $name);
        }

        // Attach CID inline logo for maximum mail client compatibility
        if (file_exists($logo_path)) {
            $mailAdmin->addEmbeddedImage($logo_path, 'brand_logo_cid', 'logo.png');
            $admin_logo_html = '<img src="cid:brand_logo_cid" alt="' . $brand_name . '" style="max-height: 55px; width: auto; display: block; border: 0;">';
        } else {
            $admin_logo_html = '<img src="' . SITE_URL . '/assets/images/logo.png" alt="' . $brand_name . '" style="max-height: 55px; width: auto; display: block; border: 0;">';
        }

        // HTML Body
        $adminHtmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Lead Notification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f6f9; padding: 25px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 620px; background-color: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.08); border-top: 6px solid #FF7A00;">
                    <!-- Logo Header -->
                    <tr>
                        <td align="center" style="padding: 30px 20px; background-color: #111111;">
                            {$admin_logo_html}
                        </td>
                    </tr>
                    <!-- Title Banner -->
                    <tr>
                        <td style="padding: 30px 40px 20px 40px;">
                            <span style="display: inline-block; background-color: rgba(255,122,0,0.12); color: #FF7A00; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px; padding: 6px 14px; border-radius: 20px; margin-bottom: 15px;">⚡ NEW SERVICE LEAD</span>
                            <h2 style="margin: 0; color: #111111; font-size: 24px; font-weight: 800; line-height: 1.3;">New Booking Received on {$brand_name}</h2>
                            <p style="color: #666666; font-size: 15px; margin-top: 10px; line-height: 1.5;">A new inquiry has been submitted via the website contact form. Customer details are below:</p>
                        </td>
                    </tr>
                    <!-- Customer Details Card -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table cellpadding="0" cellspacing="0" width="100%" style="background-color: #f9fafb; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px;">
                                <tr>
                                    <td width="35%" style="padding: 10px 0; font-size: 13px; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px; vertical-align: top;">Customer Name</td>
                                    <td style="padding: 10px 0; font-size: 16px; font-weight: 700; color: #111111; vertical-align: top;">{$name}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; font-size: 13px; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px; vertical-align: top;">Phone Number</td>
                                    <td style="padding: 10px 0; font-size: 16px; font-weight: 800; color: #FF7A00; vertical-align: top;"><a href="tel:{$clean_phone}" style="color: #FF7A00; text-decoration: none;">{$phone}</a></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; font-size: 13px; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px; vertical-align: top;">Email Address</td>
                                    <td style="padding: 10px 0; font-size: 15px; color: #111111; vertical-align: top;">" . (empty($email) ? '<em style="color:#aaa;">Not Provided</em>' : '<a href="mailto:'.$email.'" style="color:#111111; text-decoration:underline;">'.$email.'</a>') . "</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; font-size: 13px; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px; vertical-align: top;">Service Requested</td>
                                    <td style="padding: 10px 0; font-size: 15px; font-weight: 700; color: #111111; vertical-align: top;">{$service_name_en} / {$service_name_ar}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; font-size: 13px; font-weight: 700; color: #718096; text-transform: uppercase; letter-spacing: 0.5px; vertical-align: top;">Message</td>
                                    <td style="padding: 10px 0; font-size: 14px; color: #2d3748; line-height: 1.6; vertical-align: top; white-space: pre-line;">" . (empty($message) ? '<em style="color:#aaa;">No additional notes</em>' : htmlspecialchars($message, ENT_QUOTES, 'UTF-8', false)) . "</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- CTA Buttons -->
                    <tr>
                        <td style="padding: 10px 40px 35px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="{$wa_link}" target="_blank" style="display: block; background-color: #25D366; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 800; text-align: center; padding: 15px 24px; border-radius: 10px; box-shadow: 0 4px 12px rgba(37,211,102,0.25);">
                                            💬 Reply & Dispatch on WhatsApp
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <a href="tel:{$clean_phone}" style="display: block; background-color: #111111; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 800; text-align: center; padding: 15px 24px; border-radius: 10px;">
                                            📞 Call Customer Directly
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f9fafb; padding: 20px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #a0aec0;">
                            Auto-generated by {$brand_name} (ahmadfix.com).<br>
                            &copy; 2026 {$brand_name}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

        $mailAdmin->isHTML(true);
        $mailAdmin->Subject = "[NEW LEAD] {$name} — {$service_name_en} ({$brand_name})";
        $mailAdmin->Body    = $adminHtmlBody;
        $mailAdmin->AltBody = "New Lead: {$name}\nPhone: {$phone}\nEmail: {$email}\nService: {$service_name_en}\nMessage: {$message}";

        $mailAdmin->send();
        error_log("Admin notification email sent successfully to {$admin_email} for {$name}.");
    } catch (Exception $e) {
        error_log("Failed to send Admin notification email. Error: " . $e->getMessage());
    }

    // ============================================
    // 2. Send Customer Confirmation Auto-Response
    // ============================================
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $mailCustomer = new PHPMailer(true);

            // SMTP settings
            $mailCustomer->isSMTP();
            $mailCustomer->Host       = SMTP_HOST;
            $mailCustomer->SMTPAuth   = true;
            $mailCustomer->Username   = SMTP_USER;
            $mailCustomer->Password   = SMTP_PASS;
            $mailCustomer->SMTPSecure = (SMTP_SECURE === 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mailCustomer->Port       = SMTP_PORT;
            $mailCustomer->CharSet    = 'UTF-8';

            // Sender & Recipient
            $mailCustomer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mailCustomer->addAddress($email, $name);

            // Attach CID inline logo
            if (file_exists($logo_path)) {
                $mailCustomer->addEmbeddedImage($logo_path, 'cust_brand_logo_cid', 'logo.png');
                $cust_logo_html = '<img src="cid:cust_brand_logo_cid" alt="' . $brand_name . '" style="max-height: 55px; width: auto; display: block; border: 0;">';
            } else {
                $cust_logo_html = '<img src="' . SITE_URL . '/assets/images/logo.png" alt="' . $brand_name . '" style="max-height: 55px; width: auto; display: block; border: 0;">';
            }

            if ($lang === 'ar') {
                // Arabic Layout
                $subject = "تم استلام طلب الصيانة الخاص بك — أحمد فيكس (Ahmad Fix)";
                $customerHtmlBody = <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد استلام الطلب</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; text-align: right; direction: rtl;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f6f9; padding: 25px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 620px; background-color: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.08); border-top: 6px solid #FF7A00;">
                    <!-- Logo Header -->
                    <tr>
                        <td align="center" style="padding: 30px 20px; background-color: #111111;">
                            {$cust_logo_html}
                        </td>
                    </tr>
                    <!-- Success Icon & Header -->
                    <tr>
                        <td align="center" style="padding: 35px 40px 10px 40px;">
                            <div style="width: 68px; height: 68px; background-color: rgba(255,122,0,0.12); border-radius: 50%; display: inline-block; line-height: 68px; text-align: center; margin-bottom: 15px;">
                                <span style="font-size: 34px; color: #FF7A00;">✓</span>
                            </div>
                            <h2 style="margin: 0; color: #111111; font-size: 24px; font-weight: 800;">نشكرك على تواصلك مع أحمد فيكس (Ahmad Fix)</h2>
                            <p style="color: #666666; font-size: 16px; margin-top: 10px; line-height: 1.6;">مرحباً <strong>{$name}</strong>، لقد تم استلام طلب صيانة الأجهزة الخاص بك بنجاح، وفريق الدعم لدينا ينسق زيارة الفني الآن!</p>
                        </td>
                    </tr>
                    <!-- Process Steps -->
                    <tr>
                        <td style="padding: 10px 40px 20px 40px;">
                            <h3 style="color: #111111; font-size: 16px; font-weight: 800; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">خطوات الصيانة التالية:</h3>
                            <table cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td width="32" style="vertical-align: top; padding-top: 2px;">
                                        <span style="display: block; width: 22px; height: 22px; background-color: #FF7A00; color: #ffffff; border-radius: 50%; text-align: center; font-size: 13px; font-weight: 800; line-height: 22px;">١</span>
                                    </td>
                                    <td style="padding-bottom: 15px; font-size: 14px; color: #2d3748; line-height: 1.6;">
                                        <strong>تأكيد الموعد:</strong> سيتصل بك ممثل خدمة العملاء على رقم جوالك <strong>{$phone}</strong> خلال دقائق لتحديد الموعد الأنسب.
                                    </td>
                                </tr>
                                <tr>
                                    <td width="32" style="vertical-align: top; padding-top: 2px;">
                                        <span style="display: block; width: 22px; height: 22px; background-color: #FF7A00; color: #ffffff; border-radius: 50%; text-align: center; font-size: 13px; font-weight: 800; line-height: 22px;">٢</span>
                                    </td>
                                    <td style="padding-bottom: 15px; font-size: 14px; color: #2d3748; line-height: 1.6;">
                                        <strong>فحص ومعاينة:</strong> يصل الفني إلى موقعك في الدمام/الظهران/الخبر لفحص الجهاز وتقديم السعر الدقيق قبل البدء.
                                    </td>
                                </tr>
                                <tr>
                                    <td width="32" style="vertical-align: top; padding-top: 2px;">
                                        <span style="display: block; width: 22px; height: 22px; background-color: #FF7A00; color: #ffffff; border-radius: 50%; text-align: center; font-size: 13px; font-weight: 800; line-height: 22px;">٣</span>
                                    </td>
                                    <td style="font-size: 14px; color: #2d3748; line-height: 1.6;">
                                        <strong>إصلاح وضمان:</strong> نستخدم قطع غيار أصلية ونوفر ضماناً شاملاً على الصيانة.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Request Summary -->
                    <tr>
                        <td style="padding: 10px 40px 20px 40px;">
                            <table cellpadding="0" cellspacing="0" width="100%" style="background-color: #f9fafb; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; font-size: 14px;">
                                <tr>
                                    <td width="40%" style="font-weight: 700; color: #718096; padding: 6px 0;">الخدمة المطلوبة:</td>
                                    <td style="font-weight: 800; color: #111111; padding: 6px 0;">{$service_name_ar}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700; color: #718096; padding: 6px 0;">رقم الجوال:</td>
                                    <td style="color: #111111; padding: 6px 0; direction: ltr; text-align: right; font-weight: 700;">{$phone}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Urgent CTA -->
                    <tr>
                        <td style="padding: 20px 40px 35px 40px; border-bottom: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 15px 0; font-size: 14px; color: #4a5568; text-align: center;">هل تحتاج صيانة طارئة ومباشرة؟</p>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="https://wa.me/966582453440" target="_blank" style="display: block; background-color: #25D366; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 800; text-align: center; padding: 15px 24px; border-radius: 10px; box-shadow: 0 4px 12px rgba(37,211,102,0.25);">
                                            💬 تواصل معنا فوراً عبر واتساب
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <a href="tel:+966582453440" style="display: block; background-color: #FF7A00; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 800; text-align: center; padding: 15px 24px; border-radius: 10px; box-shadow: 0 4px 12px rgba(255,122,0,0.25);">
                                            📞 اتصال هاتفي مباشر
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer Info -->
                    <tr>
                        <td align="center" style="background-color: #f9fafb; padding: 24px 20px; font-size: 13px; color: #718096; line-height: 1.6;">
                            <strong>أحمد فيكس (Ahmad Fix) — صيانة الأجهزة المنزلية بالمملكة العربية السعودية</strong><br>
                            الدمام، الظهران، الخبر والمنطقة الشرقية<br>
                            ساعات العمل: السبت - الخميس: ٨:٠٠ ص - ١٠:٠٠ م | الجمعة: ٢:٠٠ م - ١٠:٠٠ م
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
            } else {
                // English Layout
                $subject = "Service Request Confirmation — Ahmad Fix (ahmadfix.com)";
                $customerHtmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Request Received</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f6f9; padding: 25px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 620px; background-color: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.08); border-top: 6px solid #FF7A00;">
                    <!-- Logo Header -->
                    <tr>
                        <td align="center" style="padding: 30px 20px; background-color: #111111;">
                            {$cust_logo_html}
                        </td>
                    </tr>
                    <!-- Success Icon & Header -->
                    <tr>
                        <td align="center" style="padding: 35px 40px 10px 40px;">
                            <div style="width: 68px; height: 68px; background-color: rgba(255,122,0,0.12); border-radius: 50%; display: inline-block; line-height: 68px; text-align: center; margin-bottom: 15px;">
                                <span style="font-size: 34px; color: #FF7A00;">✓</span>
                            </div>
                            <h2 style="margin: 0; color: #111111; font-size: 24px; font-weight: 800;">Thank You for Reaching Out to Ahmad Fix!</h2>
                            <p style="color: #666666; font-size: 16px; margin-top: 10px; line-height: 1.6;">Hello <strong>{$name}</strong>, we have received your service request. Our technical team is dispatching a certified technician to assist you!</p>
                        </td>
                    </tr>
                    <!-- Process Steps -->
                    <tr>
                        <td style="padding: 10px 40px 20px 40px;">
                            <h3 style="color: #111111; font-size: 16px; font-weight: 800; margin-bottom: 15px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">What happens next?</h3>
                            <table cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td width="32" style="vertical-align: top; padding-top: 2px;">
                                        <span style="display: block; width: 22px; height: 22px; background-color: #FF7A00; color: #ffffff; border-radius: 50%; text-align: center; font-size: 13px; font-weight: 800; line-height: 22px;">1</span>
                                    </td>
                                    <td style="padding-bottom: 15px; font-size: 14px; color: #2d3748; line-height: 1.6;">
                                        <strong>Booking Confirmation:</strong> A representative will call you on <strong>{$phone}</strong> shortly to confirm appointment details.
                                    </td>
                                </tr>
                                <tr>
                                    <td width="32" style="vertical-align: top; padding-top: 2px;">
                                        <span style="display: block; width: 22px; height: 22px; background-color: #FF7A00; color: #ffffff; border-radius: 50%; text-align: center; font-size: 13px; font-weight: 800; line-height: 22px;">2</span>
                                    </td>
                                    <td style="padding-bottom: 15px; font-size: 14px; color: #2d3748; line-height: 1.6;">
                                        <strong>Technician Visit:</strong> An expert visit to your location in Dammam/Dhahran/Khobar to diagnose and provide an upfront quote.
                                    </td>
                                </tr>
                                <tr>
                                    <td width="32" style="vertical-align: top; padding-top: 2px;">
                                        <span style="display: block; width: 22px; height: 22px; background-color: #FF7A00; color: #ffffff; border-radius: 50%; text-align: center; font-size: 13px; font-weight: 800; line-height: 22px;">3</span>
                                    </td>
                                    <td style="font-size: 14px; color: #2d3748; line-height: 1.6;">
                                        <strong>Repair & Warranty:</strong> Quality repair with genuine spare parts and service warranty.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Request Summary -->
                    <tr>
                        <td style="padding: 10px 40px 20px 40px;">
                            <table cellpadding="0" cellspacing="0" width="100%" style="background-color: #f9fafb; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; font-size: 14px;">
                                <tr>
                                    <td width="40%" style="font-weight: 700; color: #718096; padding: 6px 0;">Service Needed:</td>
                                    <td style="font-weight: 800; color: #111111; padding: 6px 0;">{$service_name_en}</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700; color: #718096; padding: 6px 0;">Phone Registered:</td>
                                    <td style="color: #111111; padding: 6px 0; font-weight: 700;">{$phone}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Urgent CTA -->
                    <tr>
                        <td style="padding: 20px 40px 35px 40px; border-bottom: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 15px 0; font-size: 14px; color: #4a5568; text-align: center;">Need urgent or immediate dispatch?</p>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding-bottom: 12px;">
                                        <a href="https://wa.me/966582453440" target="_blank" style="display: block; background-color: #25D366; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 800; text-align: center; padding: 15px 24px; border-radius: 10px; box-shadow: 0 4px 12px rgba(37,211,102,0.25);">
                                            💬 Contact Us on WhatsApp
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <a href="tel:+966582453440" style="display: block; background-color: #FF7A00; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 800; text-align: center; padding: 15px 24px; border-radius: 10px; box-shadow: 0 4px 12px rgba(255,122,0,0.25);">
                                            📞 Call Support Team
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer Info -->
                    <tr>
                        <td align="center" style="background-color: #f9fafb; padding: 24px 20px; font-size: 13px; color: #718096; line-height: 1.6;">
                            <strong>Ahmad Fix — Home Appliance Repair Services</strong><br>
                            Dammam, Dhahran, Khobar — Eastern Province, Saudi Arabia<br>
                            Hours: Sat - Thu: 8:00 AM - 10:00 PM | Fri: 2:00 PM - 10:00 PM
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
            }

            $mailCustomer->isHTML(true);
            $mailCustomer->Subject = $subject;
            $mailCustomer->Body    = $customerHtmlBody;
            $mailCustomer->AltBody = ($lang === 'ar') 
                ? "عزيزي {$name}، تم استلام طلبك لصيانة {$service_name_ar} بنجاح لدى أحمد فيكس. سنتصل بك قريباً على الرقم {$phone}."
                : "Dear {$name}, your service request for {$service_name_en} has been received by Ahmad Fix. We will contact you at {$phone} shortly.";

            $mailCustomer->send();
            error_log("Customer confirmation auto-response email sent successfully to {$email}.");
        } catch (Exception $e) {
            error_log("Failed to send Customer auto-response email to {$email}. Error: " . $e->getMessage());
        }
    }

    return true;
}

