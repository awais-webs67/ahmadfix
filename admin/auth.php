<?php
/**
 * Mr Fix KSA — Admin Authentication Guard
 */

if (!defined('FIXORA_APP')) {
    define('FIXORA_APP', true);
}

require_once dirname(__DIR__) . '/config/bootstrap.php';
require_once dirname(__DIR__) . '/config/mail.php';

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . SITE_URL . '/admin/login');
    exit;
}

// Anything saved in the admin panel → clear the cached public pages so changes show at once
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    purgePageCache();
}
