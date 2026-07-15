<?php
require_once 'includes/admin_init.php';
require_admin_login();

log_admin_action($conn, $_SESSION['admin_id'], $_SESSION['admin_username'], 'LOGOUT', 'Admin logged out.');

session_destroy();
header('Location: login.php');
exit;
