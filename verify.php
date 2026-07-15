<?php
require_once 'includes/init.php';
$page_title = "Verify Account";

$message = "";

if (isset($_GET['token'])) {
    $token = clean_input($conn, $_GET['token']);
    $result = mysqli_query($conn, "SELECT id, is_verified FROM buyers WHERE verify_token = '$token'");

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if ($row['is_verified'] == 1) {
            $message = "This account is already verified. You may now log in.";
        } else {
            mysqli_query($conn, "UPDATE buyers SET is_verified = 1 WHERE id = '" . $row['id'] . "'");
            $message = "Your account has been verified! You may now log in.";
        }
    } else {
        $message = "Invalid or expired verification link.";
    }
} else {
    $message = "No verification token provided.";
}

include 'includes/header.php';
?>
<h1>Account Verification</h1>
<p><?php echo $message; ?></p>
<p><a href="login.php">Go to Login</a></p>
<?php include 'includes/footer.php'; ?>
