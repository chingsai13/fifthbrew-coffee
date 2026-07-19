<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo isset($page_title) ? $page_title . ' - 5th Brew' : '5th Brew'; ?></title>
<link rel="stylesheet" href="<?php echo isset($base_path) ? $base_path : ''; ?>assets/style.css">
</head>
<body>
<header>
    <nav>
        <a href="<?php echo isset($base_path) ? $base_path : ''; ?>index.php" class="logo">
            <img src="<?php echo isset($base_path) ? $base_path : ''; ?>assets/logo.jpg" alt="5th Brew Logo">
            <span>5TH BREW</span>
        </a>
        <ul>
            <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>index.php">Home</a></li>
            <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>store.php">Store</a></li>
            <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>cart.php">Cart</a></li>
            <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>about.php">About</a></li>
            <?php if (buyer_is_logged_in()): ?>
                <li><span class="nav-greeting">Hi, <?php echo htmlspecialchars($_SESSION['buyer_name']); ?></span></li>
                <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>login.php">Login</a></li>
                <li><a href="<?php echo isset($base_path) ? $base_path : ''; ?>register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
