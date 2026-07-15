<?php
// Must be first thing on every page that uses sessions.
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

function buyer_is_logged_in()
{
    return isset($_SESSION['buyer_id']);
}

function require_buyer_login()
{
    if (!buyer_is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
