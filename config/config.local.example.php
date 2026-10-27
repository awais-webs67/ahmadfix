<?php
/**
 * PRIVATE settings — only the database login lives here.
 *
 * 1. Copy this file and name the copy:  config.local.php
 * 2. Fill in the database login from your hosting control panel.
 * 3. Upload config.local.php to your server (config/ folder).
 *
 * Everything else (website address, phone, email, SMTP/Gmail login, Google codes…)
 * is set in the website's Admin → Settings page — no file editing needed.
 * config.local.php is listed in .gitignore, so it is never pushed to GitHub.
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'fixora');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// Emergency only: if a wrong "Website address" was saved in Settings and the
// site keeps redirecting, uncomment this line with the right address.
// define('SITE_URL_OVERRIDE', 'https://yourdomain.com');
