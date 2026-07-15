<?php
require_once 'includes/init.php';
$page_title = "Login";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = clean_input($conn, $_POST['email']);
    $password = $_POST['password'];

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

include 'includes/header.php';
?>
<h1>Login</h1>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST" action="login.php">
    Email:<br>
    <input type="text" name="email"><br><br>
    Password:<br>
    <input type="password" name="password"><br><br>
    <input type="submit" value="Login">
</form>
<p>No account yet? <a href="register.php">Register here</a></p>

<?php include 'includes/footer.php'; ?>
