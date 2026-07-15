<?php
// ============================================================
// RUN THIS ONCE ON YOUR LIVE HOST TO CREATE YOUR FIRST ADMIN.
// DELETE THIS FILE FROM THE SERVER AFTER YOU USE IT.
// ============================================================
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = clean_input($conn, $_POST['full_name']);
    $username = clean_input($conn, $_POST['username']);
    $password = $_POST['password'];

    $check = mysqli_query($conn, "SELECT id FROM admins WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $message = "Username already exists.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO admins (full_name, username, password, role, created_at)
                              VALUES ('$full_name','$username','$hashed','superadmin', NOW())");
        $message = 'Admin account created! Go to <a href="../login.php">the main login page</a> and choose "Admin". Please delete setup_admin.php now.';
    }
}
?>
<!DOCTYPE html>
<html><body>
<h1>Create First Admin Account</h1>
<p style="color:red;">Delete this file from your server as soon as you're done using it.</p>
<?php if ($message) echo "<p>$message</p>"; ?>
<form method="POST">
    Full Name: <input type="text" name="full_name"><br><br>
    Username: <input type="text" name="username"><br><br>
    Password: <input type="password" name="password"><br><br>
    <input type="submit" value="Create Admin">
</form>
</body></html>
