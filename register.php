<?php
require_once 'includes/init.php';
$page_title = "Register";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $full_name      = clean_input($conn, $_POST['full_name']);
    $email          = clean_input($conn, $_POST['email']);
    $password       = $_POST['password'];
    $confirm_pass   = $_POST['confirm_password'];
    $address        = clean_input($conn, $_POST['address']);
    $contact_number = clean_input($conn, $_POST['contact_number']);

    if ($full_name == '' || $email == '' || $password == '' || $address == '' || $contact_number == '') {
        $errors[] = "All fields are required.";
    }
    if (!validate_email($email)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (!validate_password($password)) {
        $errors[] = "Password must be at least 8 characters and contain both letters and numbers.";
    }
    if ($password !== $confirm_pass) {
        $errors[] = "Password and Confirm Password do not match.";
    }
    if (!validate_contact_number($contact_number)) {
        $errors[] = "Please enter a valid contact number.";
    }

    // Check if email already registered
    if (empty($errors)) {
        $check = mysqli_query($conn, "SELECT id FROM buyers WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $errors[] = "That email is already registered.";
        }
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        $sql = "INSERT INTO buyers (full_name, email, password, address, contact_number, is_verified, verify_token, created_at)
                VALUES ('$full_name', '$email', '$hashed', '$address', '$contact_number', 0, '$token', NOW())";

        if (mysqli_query($conn, $sql)) {

            // Send confirmation email
            $verify_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify.php?token=" . $token;
            $subject = "Confirm your 5th Brew account";
            $message = "Hi $full_name,\n\nPlease confirm your registration by clicking this link:\n$verify_link\n\nThank you,\n5th Brew Team";
            $headers = "From: no-reply@5thbrew.com";
            @mail($email, $subject, $message, $headers);

            $success = "Registration successful! We've sent a confirmation email to " . htmlspecialchars($email) . ".";
            $success_verify_link = $verify_link;
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}

include 'includes/header.php';
?>
<h1>Register</h1>

<?php foreach ($errors as $e) echo "<p style='color:red;'>$e</p>"; ?>
<?php if (isset($success)): ?>
    <p style="color:green;"><?php echo $success; ?></p>
    <p>Didn't get the email? Free hosts can be slow or unreliable with mail delivery.
        You can verify right away instead:<br>
        <a href="<?php echo htmlspecialchars($success_verify_link); ?>">Verify my account now</a>
    </p>
<?php endif; ?>

<?php if (!isset($success)): ?>
<form method="POST" action="register.php">
    Complete Name:<br>
    <input type="text" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"><br><br>

    Email Address:<br>
    <input type="text" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"><br><br>

    Password:<br>
    <input type="password" name="password"><br><br>

    Confirm Password:<br>
    <input type="password" name="confirm_password"><br><br>

    Complete Address:<br>
    <input type="text" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>"><br><br>

    Contact Number:<br>
    <input type="text" name="contact_number" value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : ''; ?>"><br><br>

    <input type="submit" value="Register">
</form>
<?php else: ?>
    <p><a href="login.php">Go to Login</a></p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>