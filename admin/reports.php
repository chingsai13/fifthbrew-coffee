<?php
require_once 'includes/admin_init.php';
require_admin_login();
$page_title = "Reports";

$admin_id = $_SESSION['admin_id'];

$inventory = mysqli_query($conn, "
    SELECT p.id AS product_id, p.name AS product_name, po.size_label, po.temperature, po.price, po.stock
    FROM product_options po
    JOIN products p ON po.product_id = p.id
    ORDER BY p.name, po.size_label, po.temperature
");

$inventory_by_product = [];
while ($row = mysqli_fetch_assoc($inventory)) {
    $inventory_by_product[$row['product_name']][] = $row;
}

$audit = mysqli_query($conn, "
    SELECT * FROM audit_log
    WHERE admin_id = '$admin_id'
    ORDER BY created_at DESC
");

include 'includes/admin_header.php';
?>
<h1>Reports</h1>

<div class="report-tabs">
    <button type="button" class="report-tab active" data-target="inventory-report">Inventory Report</button>
    <button type="button" class="report-tab" data-target="audit-log-report">Audit Log</button>
</div>

<div id="inventory-report" class="report-panel active">
    <h2>Inventory Report</h2>

    <?php foreach ($inventory_by_product as $product_name => $rows): ?>
        <div class="inventory-product-block">
            <h3 class="inventory-product-name"><?php echo htmlspecialchars($product_name); ?></h3>
            <table>
            <tr><th>Size</th><th>Temp</th><th>Price</th><th>Remaining Stock</th></tr>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['size_label']); ?></td>
                    <td><?php echo htmlspecialchars($row['temperature']); ?></td>
                    <td><?php echo format_price($row['price']); ?></td>
                    <td class="<?php echo $row['stock'] <= 5 ? 'low-stock-value' : ''; ?>">
                        <?php echo $row['stock']; ?><?php echo $row['stock'] <= 5 ? ' ⚠ LOW' : ''; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </table>
        </div>
    <?php endforeach; ?>
</div>

<div id="audit-log-report" class="report-panel">
    <h2>Audit Log <small>(activities by <?php echo htmlspecialchars($_SESSION['admin_username']); ?>)</small></h2>
    <table>
    <tr><th>Date/Time</th><th>Action</th><th>Description</th></tr>
    <?php while ($row = mysqli_fetch_assoc($audit)): ?>
        <tr>
            <td><?php echo $row['created_at']; ?></td>
            <td><span class="action-badge"><?php echo htmlspecialchars($row['action']); ?></span></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
        </tr>
    <?php endwhile; ?>
    </table>
</div>

<script>
(function () {
    var tabs = document.querySelectorAll('.report-tab');
    var panels = document.querySelectorAll('.report-panel');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            panels.forEach(function (p) { p.classList.remove('active'); });

            tab.classList.add('active');
            document.getElementById(tab.getAttribute('data-target')).classList.add('active');
        });
    });
})();
</script>

<?php include 'includes/admin_footer.php'; ?>