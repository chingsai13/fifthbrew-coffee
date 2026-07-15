<?php
require_once 'includes/admin_init.php';
require_admin_login();
$page_title = "Reports";

$admin_id = $_SESSION['admin_id'];

$inventory = mysqli_query($conn, "
    SELECT p.name AS product_name, po.size_label, po.temperature, po.price, po.stock
    FROM product_options po
    JOIN products p ON po.product_id = p.id
    ORDER BY p.name, po.size_label
");

$audit = mysqli_query($conn, "
    SELECT * FROM audit_log
    WHERE admin_id = '$admin_id'
    ORDER BY created_at DESC
");

include 'includes/admin_header.php';
?>
<h1>Reports</h1>

<h2>Inventory Report</h2>
<table border="1" cellpadding="6">
<tr><th>Product</th><th>Size</th><th>Temp</th><th>Price</th><th>Remaining Stock</th></tr>
<?php while ($row = mysqli_fetch_assoc($inventory)): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
        <td><?php echo htmlspecialchars($row['size_label']); ?></td>
        <td><?php echo htmlspecialchars($row['temperature']); ?></td>
        <td><?php echo format_price($row['price']); ?></td>
        <td><?php echo $row['stock']; ?><?php echo $row['stock'] <= 5 ? ' ⚠ LOW' : ''; ?></td>
    </tr>
<?php endwhile; ?>
</table>

<h2>Audit Log (activities by <?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</h2>
<table border="1" cellpadding="6">
<tr><th>Date/Time</th><th>Action</th><th>Description</th></tr>
<?php while ($row = mysqli_fetch_assoc($audit)): ?>
    <tr>
        <td><?php echo $row['created_at']; ?></td>
        <td><?php echo htmlspecialchars($row['action']); ?></td>
        <td><?php echo htmlspecialchars($row['description']); ?></td>
    </tr>
<?php endwhile; ?>
</table>

<?php include 'includes/admin_footer.php'; ?>
