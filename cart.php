<?php
require_once 'includes/init.php';
require_buyer_login();
$page_title = "Cart";
$buyer_id = $_SESSION['buyer_id'];

// Handle update / remove
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['remove_id'])) {
        $id = clean_input($conn, $_POST['remove_id']);
        mysqli_query($conn, "DELETE FROM cart_items WHERE id='$id' AND buyer_id='$buyer_id'");
    } elseif (isset($_POST['update_id'])) {
        $id = clean_input($conn, $_POST['update_id']);
        $qty = (int) $_POST['quantity'];
        if ($qty < 1) $qty = 1;
        mysqli_query($conn, "UPDATE cart_items SET quantity='$qty' WHERE id='$id' AND buyer_id='$buyer_id'");
    }
}

$sql = "SELECT ci.id, ci.quantity, po.size_label, po.temperature, po.price, p.name
        FROM cart_items ci
        JOIN product_options po ON ci.product_option_id = po.id
        JOIN products p ON po.product_id = p.id
        WHERE ci.buyer_id = '$buyer_id'";
$result = mysqli_query($conn, $sql);
$total = 0;

include 'includes/header.php';
?>
<h1>Your Cart</h1>

<table border="1" cellpadding="6">
<tr><th>Product</th><th>Size</th><th>Temp</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr>
<?php while ($row = mysqli_fetch_assoc($result)):
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
?>
    <tr>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['size_label']); ?></td>
        <td><?php echo htmlspecialchars($row['temperature']); ?></td>
        <td><?php echo format_price($row['price']); ?></td>
        <td>
            <form method="POST" action="cart.php" style="display:inline;">
                <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1" style="width:50px;">
                <input type="hidden" name="update_id" value="<?php echo $row['id']; ?>">
                <input type="submit" value="Update">
            </form>
        </td>
        <td><?php echo format_price($subtotal); ?></td>
        <td>
            <form method="POST" action="cart.php" style="display:inline;">
                <input type="hidden" name="remove_id" value="<?php echo $row['id']; ?>">
                <input type="submit" value="Remove">
            </form>
        </td>
    </tr>
<?php endwhile; ?>
</table>

<h3>Total: <?php echo format_price($total); ?></h3>

<?php if ($total > 0): ?>
    <p><a href="checkout.php">Proceed to Checkout</a></p>
<?php else: ?>
    <p>Your cart is empty. <a href="store.php">Go shopping</a></p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
