<?php
// ============================================================
// SHARED FUNCTIONS
// ============================================================

// Straight from the M5 slide's regex email validation example.
function validate_email($email_address)
{
    if (!preg_match("/^([a-zA-Z0-9]+([a-zA-Z0-9._-]*[a-zA-Z0-9_-]+)*)@([a-zA-Z0-9_-]+\.)+[a-zA-Z0-9_-]+$/", $email_address)) {
        return false;
    }
    return true;
}

// Simple input cleaner used on every form field before it hits the DB.
function clean_input($conn, $value)
{
    $value = trim($value);
    $value = stripslashes($value);
    return mysqli_real_escape_string($conn, $value);
}

// Philippine-style contact number validation: digits, 7-15 characters,
// may start with a plus sign.
function validate_contact_number($number)
{
    if (!preg_match("/^\+?[0-9]{7,15}$/", $number)) {
        return false;
    }
    return true;
}

// Password rule: at least 8 characters, at least one letter and one number.
function validate_password($password)
{
    if (!preg_match("/^(?=.*[A-Za-z])(?=.*[0-9]).{8,}$/", $password)) {
        return false;
    }
    return true;
}

function format_price($amount)
{
    return "₱" . number_format($amount, 2);
}

// Writes one row to audit_log for the currently logged-in admin.
function log_admin_action($conn, $admin_id, $admin_username, $action, $description)
{
    $action = clean_input($conn, $action);
    $description = clean_input($conn, $description);
    $sql = "INSERT INTO audit_log (admin_id, admin_username, action, description, created_at)
            VALUES ('$admin_id', '$admin_username', '$action', '$description', NOW())";
    mysqli_query($conn, $sql);
}
