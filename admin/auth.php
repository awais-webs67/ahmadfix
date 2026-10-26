<?php
/**
 * Ahmad Fix — Admin Authentication Guard
 */

if (!defined('FIXORA_APP')) {
    define('FIXORA_APP', true);
}

require_once dirname(__DIR__) . '/config/config.php';

header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ' . SITE_URL . '/admin/login');
    exit;
}
