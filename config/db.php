<?php
// ============================================================
// DATABASE CONNECTION - EDIT THESE 4 VALUES AFTER YOU CREATE
// YOUR DATABASE ON YOUR FREE HOST (InfinityFree / AwardSpace).
// ============================================================
$db_host = "localhost";      // e.g. sqlXXX.infinityfree.com
$db_user = "your_db_user";   // from your host's MySQL panel
$db_pass = "your_db_pass";   // from your host's MySQL panel
$db_name = "your_db_name";   // from your host's MySQL panel

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
