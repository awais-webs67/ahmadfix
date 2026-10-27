<?php
/**
 * Ahmad Fix — Database Configuration & Core Setup
 * Connects to the 'fixora' MySQL database via PDO
 *
 * IMPORTANT: passwords (database + email) are NOT stored in this file.
 * Put them in config/config.local.php (copy config.local.example.php).
 * That file is ignored by git, so it never becomes public.
 */

// Prevent direct access
if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

// ============================================
// Private settings (passwords) — loaded first so they win
// ============================================
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

/**
 * Define a constant only if config.local.php did not already set it.
 */
function fixora_define($name, $value) {
    if (!defined($name)) {
        define($name, $value);
    }
}

// ============================================
// Database Configuration (override in config.local.php)
// ============================================
fixora_define('DB_HOST', 'localhost');
fixora_define('DB_NAME', 'fixora');
fixora_define('DB_USER', 'root');
fixora_define('DB_PASS', '');
fixora_define('DB_CHARSET', 'utf8mb4');

// ============================================
// Site Configuration
// ============================================
fixora_define('SITE_NAME', 'Ahmad Fix');
fixora_define('SITE_NAME_AR', 'أحمد فيكس');
fixora_define('SITE_DOMAIN', 'ahmadfix.com');

// Auto-detect protocol, host, and folder path for local/live server compatibility
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$folder = trim($script_dir, '/');
// Admin pages and sitemap live in sub-folders / root files — always use the project root
$folder = trim(preg_replace('#(^|/)admin$#', '', $folder), '/');

define('IS_LOCAL', strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);

// Live site: never show PHP errors to visitors (they are written to the server error log instead)
if (!IS_LOCAL) {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
}

if (IS_LOCAL) {
    define('SITE_URL', $protocol . $host . ($folder ? '/' . $folder : ''));
} else {
    define('SITE_URL', 'https://' . SITE_DOMAIN);
}

define('SITE_ROOT', dirname(__DIR__));
define('ASSETS_URL', SITE_URL . '/assets');
// Bump this when CSS/JS change so visitors get the new files (cache busting)
define('ASSETS_VERSION', '2.1.1');

// Default contact info (the admin panel can change phone / WhatsApp / email — see below)
fixora_define('DEFAULT_PHONE', '+966 58 245 3440');
fixora_define('DEFAULT_WHATSAPP', '+966582453440');
fixora_define('DEFAULT_EMAIL', 'ahmadarian538@gmail.com');
fixora_define('ADMIN_EMAIL', 'ahmadarian538@gmail.com');

// ============================================
// Google & Social (fill these in — empty values are simply skipped)
// ============================================
// Google Analytics 4 Measurement ID, e.g. 'G-XXXXXXXXXX'
fixora_define('GA4_ID', '');
// Google Search Console "HTML tag" verification code (only the content="..." value)
fixora_define('GOOGLE_SITE_VERIFICATION', '');
// Bing Webmaster Tools verification code (optional)
fixora_define('BING_SITE_VERIFICATION', '');
// Your Google Business Profile link (share link from Google Maps), e.g. 'https://maps.app.goo.gl/xxxx'
fixora_define('GOOGLE_BUSINESS_URL', '');
// Social profiles (full URLs). Leave empty if you don't have one.
fixora_define('SOCIAL_INSTAGRAM', '');
fixora_define('SOCIAL_SNAPCHAT', '');
fixora_define('SOCIAL_TIKTOK', '');
fixora_define('SOCIAL_FACEBOOK', '');
fixora_define('SOCIAL_X', '');

// ============================================
// SMTP Configuration for Email Notifications
// Set SMTP_USER / SMTP_PASS in config.local.php (never here)
// ============================================
fixora_define('SMTP_HOST', 'smtp.gmail.com');
fixora_define('SMTP_PORT', 587);
fixora_define('SMTP_SECURE', 'tls'); // 'tls' or 'ssl'
fixora_define('SMTP_USER', '');
fixora_define('SMTP_PASS', '');
fixora_define('SMTP_FROM_EMAIL', SMTP_USER ?: DEFAULT_EMAIL);
fixora_define('SMTP_FROM_NAME', 'Ahmad Fix');
// Emails are sent only when a password has been configured
fixora_define('SMTP_ENABLED', SMTP_USER !== '' && SMTP_PASS !== '');

// ============================================
// Database Connection (PDO)
// ============================================
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $pdo->exec("SET NAMES utf8mb4");
    $pdo->exec("SET CHARACTER SET utf8mb4");
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    http_response_code(503);
    header('Retry-After: 600');
    die('<h1>Service Temporarily Unavailable</h1><p>Please try again later.</p>');
}

// ============================================
// Session Management (secure cookie settings)
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !IS_LOCAL,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ============================================
// Language Detection & Setup
// ============================================
$supported_langs = ['en', 'ar'];
$default_lang = 'en';

// Check URL prefix first, then session, then browser
if (isset($url_lang) && in_array($url_lang, $supported_langs)) {
    $lang = $url_lang;
} elseif (isset($_GET['lang']) && in_array($_GET['lang'], $supported_langs)) {
    $lang = $_GET['lang'];
} elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $supported_langs)) {
    $lang = $_SESSION['lang'];
} else {
    // Detect from browser Accept-Language
    $browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
    $lang = in_array($browser_lang, $supported_langs) ? $browser_lang : $default_lang;
}

$_SESSION['lang'] = $lang;
$is_rtl = ($lang === 'ar');
$dir = $is_rtl ? 'rtl' : 'ltr';

// ============================================
// Load Language File
// ============================================
$lang_file = SITE_ROOT . '/lang/' . $lang . '.php';
if (file_exists($lang_file)) {
    $translations = require $lang_file;
} else {
    $translations = require SITE_ROOT . '/lang/en.php';
}

// ============================================
// Load Helper Functions + run one-time database upgrades
// ============================================
require_once SITE_ROOT . '/config/functions.php';
require_once SITE_ROOT . '/config/migrations.php';
runMigrations($pdo);

// ============================================
// Contact info: admin panel values (database) win over defaults
// ============================================
$__phone = trim(setting('phone', 'en'));
$__wa    = trim(setting('whatsapp', 'en'));
$__email = trim(setting('email', 'en'));
define('PHONE', $__phone !== '' ? $__phone : DEFAULT_PHONE);
define('WHATSAPP', $__wa !== '' ? $__wa : DEFAULT_WHATSAPP);
define('EMAIL', ($__email !== '' && filter_var($__email, FILTER_VALIDATE_EMAIL)) ? $__email : DEFAULT_EMAIL);
unset($__phone, $__wa, $__email);

// ============================================
// CSRF Token
// ============================================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
