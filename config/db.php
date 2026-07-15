<?php
// ============================================================
// DATABASE CONNECTION - EDIT THESE 4 VALUES AFTER YOU CREATE
// YOUR DATABASE ON YOUR FREE HOST (InfinityFree / AwardSpace).
// ============================================================
$db_host = "sql102.infinityfree.com";      // e.g. sqlXXX.infinityfree.com
$db_user = "if0_42415542";   // from your host's MySQL panel
$db_pass = "Wishbone13333";   // from your host's MySQL panel
$db_name = "if0_42415542_fifthbrewdb";   // from your host's MySQL panel

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
