<?php
require_once 'includes/admin_init.php';
require_admin_login();
$page_title = "Dashboard";

$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM products"))['c'];
$total_buyers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM buyers"))['c'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM orders"))['c'];
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM product_options WHERE stock <= 5"))['c'];

$low_stock_items = mysqli_query($conn, "
    SELECT p.name AS product_name, po.size_label, po.temperature, po.stock
    FROM product_options po
    JOIN products p ON po.product_id = p.id
    WHERE po.stock <= 5
    ORDER BY po.stock ASC
");

include 'includes/admin_header.php';
?>
<h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_fullname']); ?></h1>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon-wrap">
            <div class="stat-icon">📦</div>
        </div>
        <div class="stat-info">
            <span class="stat-number"><?php echo $total_products; ?></span>
            <span class="stat-label">Total Products</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap">
            <div class="stat-icon">👥</div>
        </div>
        <div class="stat-info">
            <span class="stat-number"><?php echo $total_buyers; ?></span>
            <span class="stat-label">Registered Buyers</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrap">
            <div class="stat-icon">🧾</div>
        </div>
        <div class="stat-info">
            <span class="stat-number"><?php echo $total_orders; ?></span>
            <span class="stat-label">Total Orders</span>
        </div>
    </div>

    <div class="stat-card stat-card-warning">
        <div class="stat-icon-wrap">
            <div class="stat-icon">⚠️</div>
        </div>
        <div class="stat-info">
            <span class="stat-number"><?php echo $low_stock; ?></span>
            <span class="stat-label">Low Stock Items <small>(5 or fewer left)</small></span>
        </div>
    </div>
</div>

<?php if ($low_stock > 0): ?>
<h2>Low Stock Drinks</h2>
<table>
<tr><th>Product</th><th>Size</th><th>Temp</th><th>Remaining Stock</th></tr>
<?php while ($row = mysqli_fetch_assoc($low_stock_items)): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
        <td><?php echo htmlspecialchars($row['size_label']); ?></td>
        <td><?php echo htmlspecialchars($row['temperature']); ?></td>
        <td class="low-stock-value"><?php echo $row['stock']; ?></td>
    </tr>
<?php endwhile; ?>
</table>
<?php endif; ?>

<?php include 'includes/admin_footer.php'; ?>