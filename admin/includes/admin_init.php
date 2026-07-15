<?php
session_start();

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

function admin_is_logged_in()
{
    return isset($_SESSION['admin_id']);
}

function require_admin_login()
{
    if (!admin_is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
