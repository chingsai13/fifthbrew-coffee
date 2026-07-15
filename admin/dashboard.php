<?php
require_once 'includes/admin_init.php';
require_admin_login();
$page_title = "Dashboard";

$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM products"))['c'];
$total_buyers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM buyers"))['c'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM orders"))['c'];
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM product_options WHERE stock <= 5"))['c'];

include 'includes/admin_header.php';
?>
<h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_fullname']); ?></h1>

<ul>
    <li>Total Products: <?php echo $total_products; ?></li>
    <li>Total Registered Buyers: <?php echo $total_buyers; ?></li>
    <li>Total Orders: <?php echo $total_orders; ?></li>
    <li>Low Stock Items (5 or fewer left): <?php echo $low_stock; ?></li>
</ul>

<?php include 'includes/admin_footer.php'; ?>
