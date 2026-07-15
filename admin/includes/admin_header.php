<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo isset($page_title) ? $page_title . ' - 5th Brew Admin' : '5th Brew Admin'; ?></title>
</head>
<body>
<header>
    <img src="../assets/logo.png" alt="5th Brew Logo" width="60">
    <strong>5TH BREW - ADMIN PANEL</strong> &nbsp;|&nbsp;
    <a href="dashboard.php">Dashboard</a> |
    <a href="manage_users.php">Manage Admin Users</a> |
    <a href="manage_stocks.php">Manage Stocks</a> |
    <a href="reports.php">Reports</a> |
    <?php if (admin_is_logged_in()): ?>
        Logged in as <?php echo htmlspecialchars($_SESSION['admin_username']); ?> |
        <a href="logout.php">Logout</a>
    <?php endif; ?>
</header>
<hr>
