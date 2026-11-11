<?php
/**
 * YOUR PRIVATE SETTINGS — database login and (optionally) email login.
 *
 * HOW TO DEPLOY OR MOVE TO ANY NEW HOSTING (3 STEPS ONLY):
 * 1. Deploy/clone this repository to your new hosting folder (e.g. public_html).
 * 2. Import your old database backup (.sql) into your new MySQL database.
 * 3. Copy your old config/config.php (or copy this file to config/config.php)
 *    and update DB_HOST, DB_NAME, DB_USER, DB_PASS for the new server.
 *
 * config/config.php is ignored by git: it is never uploaded to GitHub, and
 * Git pull / cPanel "Update from Remote" never overwrites it.
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

// ---- Database Login (Update these 4 lines when moving to a new host) ----
define('DB_HOST', 'localhost');        // Usually 'localhost' or '127.0.0.1' (supports 'host:port' too)
define('DB_PORT', '');                 // Leave '' for default 3306, or set e.g. '3307' / '25060' if required
define('DB_NAME', 'fixora');           // Your hosting MySQL database name (e.g. 'u123456789_fixora')
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// ---- Email (SMTP) ----
// If your hosting blocks Gmail ("Connection refused" in the test email), uncomment the next line:
// it sends through the hosting server's own mail system and needs no password.
// define('MAIL_TRANSPORT', 'phpmail');
//
// Otherwise just fill SMTP_USER and SMTP_PASS — the server is found automatically
// (or edit everything in Admin → Settings → Email, which writes into this file).
define('SMTP_HOST', '');               // '' = automatic (from your email address)
define('SMTP_PORT', 587);              // 587 with 'tls', or 465 with 'ssl'
define('SMTP_SECURE', 'tls');          // 'tls', 'ssl' or 'none'
define('SMTP_USER', '');               // Gmail or business email, e.g. 'contact@yourdomain.com'
define('SMTP_PASS', '');               // mailbox password (Gmail: 16-letter App password)
define('SMTP_FROM_EMAIL', '');         // '' = same as SMTP_USER
define('SMTP_FROM_NAME', '');          // '' = business name from Settings
define('NOTIFY_EMAILS', '');           // where new-lead alerts go ('' = Settings)
// define('AUTO_REPLY_ENABLED', true); // uncomment to force the customer "Thank you" email on (true) or off (false)

// Emergency / Staging Override: if moving to a temporary staging URL or new domain before
// updating Admin → Settings → Website address, uncomment this line:
// define('SITE_URL_OVERRIDE', 'https://yourdomain.com');
