<?php
/**
 * Ahmad Fix — Admin Logout
 */

if (!defined('FIXORA_APP')) {
    define('FIXORA_APP', true);
}

require_once dirname(__DIR__) . '/config/config.php';

unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);

header('Location: ' . SITE_URL . '/admin/login');
exit;
