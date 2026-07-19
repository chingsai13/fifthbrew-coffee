<?php
// ============================================================
// DATABASE CONNECTION - EDIT THESE 4 VALUES AFTER YOU CREATE
// YOUR DATABASE ON YOUR FREE HOST (InfinityFree / AwardSpace).
// ============================================================
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "fifthbrewdb";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
