<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo isset($page_title) ? $page_title . ' - 5th Brew Admin' : '5th Brew Admin'; ?></title>
<link rel="stylesheet" href="../assets/admin.css">
</head>
<body>
<aside class="admin-sidebar">
    <div class="admin-brand">
        <img src="../assets/logo.png" alt="5th Brew Logo">
        <div class="admin-brand-text">
            <strong>5TH BREW</strong>
            <span>Admin Panel</span>
        </div>
    </div>

    <ul class="admin-nav">
        <li><a href="dashboard.php" class="<?php echo $page_title == 'Dashboard' ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="manage_users.php" class="<?php echo $page_title == 'Manage Admin Users' ? 'active' : ''; ?>">Manage Admin Users</a></li>
        <li><a href="manage_stocks.php" class="<?php echo $page_title == 'Manage Stocks' ? 'active' : ''; ?>">Manage Stocks</a></li>
        <li><a href="reports.php" class="<?php echo $page_title == 'Reports' ? 'active' : ''; ?>">Reports</a></li>
    </ul>

    <?php if (admin_is_logged_in()): ?>
        <div class="admin-sidebar-footer">
            <strong>Logged in as</strong>
            <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
            <br>
            <a href="logout.php" class="admin-logout">Logout</a>
        </div>
    <?php endif; ?>
</aside>

<main class="admin-main">
    <div class="admin-content">