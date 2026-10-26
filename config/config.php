<?php
/**
 * Ahmad Fix — Database Configuration & Core Setup
 * Connects to the 'fixora' MySQL database via PDO
 */

// Prevent direct access
if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

// ============================================
// Database Configuration
// ============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'fixora');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ============================================
// Site Configuration
// ============================================
define('SITE_NAME', 'Ahmad Fix');
define('SITE_DOMAIN', 'ahmadfix.com');

// Auto-detect protocol, host, and folder path for local/live server compatibility
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$folder = trim($script_dir, '/');

if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
    define('SITE_URL', $protocol . $host . ($folder ? '/' . $folder : ''));
} else {
    define('SITE_URL', 'https://' . SITE_DOMAIN);
}

define('SITE_ROOT', dirname(__DIR__));
define('ASSETS_URL', SITE_URL . '/assets');

// Contact Info & Admin Email
define('PHONE', '+966 58 245 3440');
define('WHATSAPP', '+966582453440');
define('EMAIL', 'ahmadarian538@gmail.com');
define('ADMIN_EMAIL', 'ahmadarian538@gmail.com');

// ============================================
// SMTP Configuration for Email Notifications
// ============================================
define('SMTP_ENABLED', true); // Set to true to send email notifications on contact submissions
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // 'tls' or 'ssl'
define('SMTP_USER', 'ahmadarian538@gmail.com'); // Put your SMTP username here
define('SMTP_PASS', 'fbrk uxzw fdnv jjag');   // Put your SMTP password/app-password here
define('SMTP_FROM_EMAIL', 'ahmadarian538@gmail.com'); // From email for customer response
define('SMTP_FROM_NAME', 'Ahmad Fix');

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
    die('<h1>Service Temporarily Unavailable</h1><p>Please try again later.</p>');
}

// ============================================
// Session Management
// ============================================
if (session_status() === PHP_SESSION_NONE) {
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
// Load Helper Functions
// ============================================
require_once SITE_ROOT . '/config/functions.php';

// ============================================
// CSRF Token
// ============================================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
