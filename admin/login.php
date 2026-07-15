<?php
require_once 'includes/admin_init.php';
$page_title = "Admin Login";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM admins WHERE username='$username'");

    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_fullname'] = $admin['full_name'];

            log_admin_action($conn, $admin['id'], $admin['username'], 'LOGIN', 'Admin logged in.');

            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Incorrect username or password.";
        }
    } else {
        $error = "Incorrect username or password.";
    }
}
?>
<!DOCTYPE html>
<html><body>
<h1>5th Brew Admin Login</h1>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST" action="login.php">
    Username: <input type="text" name="username"><br><br>
    Password: <input type="password" name="password"><br><br>
    <input type="submit" value="Login">
</form>
</body></html>
