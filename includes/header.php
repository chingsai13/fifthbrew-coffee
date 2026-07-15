<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo isset($page_title) ? $page_title . ' - 5th Brew' : '5th Brew'; ?></title>
</head>
<body>
<header>
    <img src="assets/logo.png" alt="5th Brew Logo" width="60">
    <strong>5TH BREW</strong> &nbsp;|&nbsp;
    <a href="index.php">Home</a> |
    <a href="store.php">Store</a> |
    <a href="cart.php">Cart</a> |
    <a href="about.php">About</a> |
    <?php if (buyer_is_logged_in()): ?>
        Hi, <?php echo htmlspecialchars($_SESSION['buyer_name']); ?> |
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
    <?php endif; ?>
</header>
<hr>
