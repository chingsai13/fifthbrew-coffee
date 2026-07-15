<?php
require_once 'includes/init.php';
require_buyer_login();
$page_title = "Checkout";
$buyer_id = $_SESSION['buyer_id'];

$buyer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buyers WHERE id='$buyer_id'"));

$sql = "SELECT ci.quantity, po.price, po.size_label, po.temperature, p.name
        FROM cart_items ci
        JOIN product_options po ON ci.product_option_id = po.id
        JOIN products p ON po.product_id = p.id
        WHERE ci.buyer_id = '$buyer_id'";
$result = mysqli_query($conn, $sql);
$total = 0;
$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $items[] = $row;
}

if (empty($items)) {
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Save delivery details in session, then go to payment page
    $_SESSION['checkout_name'] = clean_input($conn, $_POST['full_name']);
    $_SESSION['checkout_address'] = clean_input($conn, $_POST['address']);
    $_SESSION['checkout_contact'] = clean_input($conn, $_POST['contact_number']);
    header('Location: payment.php');
    exit;
}

include 'includes/header.php';
?>
<h1>Checkout</h1>

<h3>Order Summary</h3>
<table border="1" cellpadding="6">
<tr><th>Product</th><th>Size</th><th>Temp</th><th>Qty</th><th>Subtotal</th></tr>
<?php foreach ($items as $it): ?>
    <tr>
        <td><?php echo htmlspecialchars($it['name']); ?></td>
        <td><?php echo htmlspecialchars($it['size_label']); ?></td>
        <td><?php echo htmlspecialchars($it['temperature']); ?></td>
        <td><?php echo $it['quantity']; ?></td>
        <td><?php echo format_price($it['subtotal']); ?></td>
    </tr>
<?php endforeach; ?>
</table>
<h3>Total: <?php echo format_price($total); ?></h3>

<h3>Delivery Details</h3>
<form method="POST" action="checkout.php">
    Complete Name:<br>
    <input type="text" name="full_name" value="<?php echo htmlspecialchars($buyer['full_name']); ?>"><br><br>
    Complete Address:<br>
    <input type="text" name="address" value="<?php echo htmlspecialchars($buyer['address']); ?>"><br><br>
    Contact Number:<br>
    <input type="text" name="contact_number" value="<?php echo htmlspecialchars($buyer['contact_number']); ?>"><br><br>
    <input type="submit" value="Continue to Payment">
</form>

<?php include 'includes/footer.php'; ?>
