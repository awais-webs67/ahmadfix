<?php
/**
 * YOUR PRIVATE SETTINGS — database login and (optionally) email login.
 *
 * 1. Copy this file and name the copy:  config.php   (same config/ folder)
 * 2. Fill in your values below and save.
 *
 * config/config.php is ignored by git: it is never uploaded to GitHub, and
 * cPanel "Update from Remote" never changes it or complains about it.
 * Edit it on the server whenever you like (cPanel → File Manager → Edit).
 *
 * Email (SMTP): values filled in here win and show as locked in
 * Admin → Settings → Email. Leave a value '' to manage it from the admin
 * panel instead. Everything else is set in Admin → Settings.
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'fixora');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// ---- Email (SMTP) ----
// Gmail: create an App password at myaccount.google.com → Security →
// 2-Step Verification → App passwords (16 letters, spaces are fine).
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);              // 587 with 'tls', or 465 with 'ssl'
define('SMTP_SECURE', 'tls');          // 'tls', 'ssl' or 'none'
define('SMTP_USER', '');               // your Gmail address, e.g. 'name@gmail.com'
define('SMTP_PASS', '');               // the 16-letter App password
define('SMTP_FROM_EMAIL', '');         // '' = same as SMTP_USER
define('SMTP_FROM_NAME', '');          // '' = business name from Settings
define('NOTIFY_EMAILS', '');           // where new-lead alerts go, e.g. 'you@gmail.com, partner@gmail.com' ('' = Settings)
// define('AUTO_REPLY_ENABLED', true); // uncomment to force the customer "Thank you" email on (true) or off (false)

// Emergency only: if a wrong "Website address" was saved in Settings and the
// site keeps redirecting, uncomment this line with the right address.
// define('SITE_URL_OVERRIDE', 'https://yourdomain.com');
