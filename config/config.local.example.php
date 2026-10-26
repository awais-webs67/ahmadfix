<?php
/**
 * Ahmad Fix — PRIVATE settings
 *
 * 1. Copy this file and name the copy:  config.local.php
 * 2. Fill in your real values below.
 * 3. Upload config.local.php to your server (config/ folder).
 *
 * config.local.php is listed in .gitignore, so it is never pushed to GitHub.
 * NEVER put real passwords in config.php or in this example file.
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

// ---- Database (from your hosting control panel) ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'fixora');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// ---- Gmail SMTP (for new-lead emails) ----
// Create a NEW Gmail "App password": Google Account > Security > 2-Step Verification > App passwords
define('SMTP_USER', 'ahmadarian538@gmail.com');
define('SMTP_PASS', 'xxxx xxxx xxxx xxxx');

// ---- Optional: Google tools (you can also set these in config.php) ----
// define('GA4_ID', 'G-XXXXXXXXXX');
// define('GOOGLE_SITE_VERIFICATION', 'paste-code-here');
// define('GOOGLE_BUSINESS_URL', 'https://maps.app.goo.gl/xxxx');
