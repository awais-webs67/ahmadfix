<?php
/**
 * Ahmad Fix — Admin Logout
 */

if (!defined('FIXORA_APP')) {
    define('FIXORA_APP', true);
}

require_once dirname(__DIR__) . '/config/config.php';

unset($_SESSION['admin_logged_in'], $_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['must_change_password']);
session_regenerate_id(true);

header('Location: ' . SITE_URL . '/admin/login');
exit;
