<?php
require_once 'includes/init.php';
$page_title = "Login";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_as = isset($_POST['login_as']) ? $_POST['login_as'] : 'buyer';
    $password = $_POST['password'];

    if ($login_as == 'admin') {
        // ---------------- ADMIN LOGIN ----------------
        $username = clean_input($conn, $_POST['identifier']);
        $result = mysqli_query($conn, "SELECT * FROM admins WHERE username = '$username'");

        if (mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_fullname'] = $admin['full_name'];

                log_admin_action($conn, $admin['id'], $admin['username'], 'LOGIN', 'Admin logged in.');

                header('Location: admin/dashboard.php');
                exit;
            } else {
                $error = "Incorrect username or password.";
            }
        } else {
            $error = "Incorrect username or password.";
        }

    } else {
        // ---------------- BUYER LOGIN ----------------
        $email = clean_input($conn, $_POST['identifier']);
        $result = mysqli_query($conn, "SELECT * FROM buyers WHERE email = '$email'");

        if (mysqli_num_rows($result) == 1) {
            $buyer = mysqli_fetch_assoc($result);

            if (!password_verify($password, $buyer['password'])) {
                $error = "Incorrect email or password.";
            } elseif ($buyer['is_verified'] == 0) {
                $error = "Please verify your email first. Check your inbox for the confirmation link.";
            } else {
                $_SESSION['buyer_id'] = $buyer['id'];
                $_SESSION['buyer_name'] = $buyer['full_name'];
                header('Location: store.php');
                exit;
            }
        } else {
            $error = "Incorrect email or password.";
        }
    }
}

include 'includes/header.php';
?>
<div class="page-wrap">
    <div class="form-card">
        <h1>Login</h1>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Log in as:</label>
                <div class="radio-row">
                    <label><input type="radio" name="login_as" value="buyer" checked> Customer</label>
                    <label><input type="radio" name="login_as" value="admin"> Admin</label>
                </div>
            </div>

            <div class="form-group">
                <label>Email (Customer) / Username (Admin)</label>
                <input type="text" name="identifier">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password">
            </div>

            <input type="submit" value="Login">
        </form>
        <p class="form-footnote">No account yet? <a href="register.php">Register as a customer here</a></p>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
