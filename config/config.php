<?php
/**
 * Ahmad Fix — Core Setup
 *
 * Nothing about the business is written here: name, website address, phone,
 * email, SMTP, Google codes, social links etc. are all edited in
 * Admin → Settings and stored in the database.
 *
 * The ONLY thing this file needs is how to reach the database. Put your real
 * database login in config/config.local.php (copy config.local.example.php).
 * That file is ignored by git, so it never becomes public.
 */

// Prevent direct access
if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

// ============================================
// Private settings (database login) — loaded first so they win
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
// Database (defaults suit XAMPP/WAMP; override in config.local.php)
// ============================================
fixora_define('DB_HOST', 'localhost');
fixora_define('DB_NAME', 'fixora');
fixora_define('DB_USER', 'root');
fixora_define('DB_PASS', '');
fixora_define('DB_CHARSET', 'utf8mb4');

define('SITE_ROOT', dirname(__DIR__));
// Bump this when CSS/JS change so visitors get the new files (cache busting)
define('ASSETS_VERSION', '2.2.0');

// ============================================
// Who is asking? (host, https, local or live)
// ============================================
$host = strtolower($_SERVER['HTTP_HOST'] ?? 'localhost');
$host = preg_replace('/[^a-z0-9.\-:\[\]]/', '', $host); // never trust odd characters in Host
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
    || (($_SERVER['SERVER_PORT'] ?? '') == 443);
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
// Admin pages live in a sub-folder — always use the project root
$folder = trim(preg_replace('#(^|/)admin$#', '', trim($script_dir, '/')), '/');

define('IS_LOCAL', (bool)preg_match('/^(localhost|127\.0\.0\.1|\[::1\])(:\d+)?$/', $host));
define('IS_HTTPS', $is_https);
define('IS_ADMIN_REQUEST', str_starts_with(trim($_GET['route'] ?? '', '/'), 'admin') || str_contains($script_dir, '/admin'));

// Live site: never show PHP errors to visitors (they are written to the server error log instead)
if (!IS_LOCAL) {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
}

// The address this request came in on (used when no website address is saved yet)
define('REQUEST_BASE_URL', ($is_https ? 'https://' : 'http://') . $host . ($folder ? '/' . $folder : ''));

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
        'secure'   => IS_HTTPS,
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
// Website address (Admin → Settings → Business → Website address)
// ============================================
$__site_url = rtrim(trim(setting('site_url', 'en')), '/');
if (defined('SITE_URL_OVERRIDE')) {
    $__site_url = rtrim(SITE_URL_OVERRIDE, '/');        // emergency override from config.local.php
}
$__valid = $__site_url !== '' && filter_var($__site_url, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $__site_url);

// Local testing and the admin panel always use the address in the browser,
// so a wrong saved address can never lock you out of the admin panel.
define('SITE_URL_CONFIGURED', $__valid && !IS_LOCAL);
define('SITE_URL', (SITE_URL_CONFIGURED && !IS_ADMIN_REQUEST) ? $__site_url : REQUEST_BASE_URL);
define('SITE_DOMAIN', parse_url($__valid ? $__site_url : REQUEST_BASE_URL, PHP_URL_HOST) ?: $host);
define('ASSETS_URL', SITE_URL . '/assets');
unset($__site_url, $__valid);

// ============================================
// Business details (all from Admin → Settings)
// ============================================
$__schema = settingsSchema();
fixora_define('SITE_NAME', trim(setting('site_name', 'en')) ?: $__schema['business']['fields']['site_name']['default'][0]);
fixora_define('SITE_NAME_AR', trim(setting('site_name', 'ar')) ?: SITE_NAME);
define('PHONE', trim(setting('phone', 'en')) ?: $__schema['business']['fields']['phone']['default']);
define('WHATSAPP', trim(setting('whatsapp', 'en')) ?: PHONE);
$__email = trim(setting('email', 'en'));
define('EMAIL', filter_var($__email, FILTER_VALIDATE_EMAIL) ? $__email : $__schema['business']['fields']['email']['default']);
unset($__schema, $__email);

// ============================================
// CSRF Token
// ============================================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
