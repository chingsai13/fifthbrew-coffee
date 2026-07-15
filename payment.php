<?php
require_once 'includes/init.php';
require_buyer_login();
$page_title = "Payment";
$buyer_id = $_SESSION['buyer_id'];

if (!isset($_SESSION['checkout_name'])) {
    header('Location: checkout.php');
    exit;
}

$sql = "SELECT ci.id AS cart_id, ci.quantity, po.id AS option_id, po.price, po.stock, po.size_label, po.temperature, p.name
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

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = clean_input($conn, $_POST['payment_method']);
    $name = $_SESSION['checkout_name'];
    $address = $_SESSION['checkout_address'];
    $contact = $_SESSION['checkout_contact'];

    // Create the order
    mysqli_query($conn, "INSERT INTO orders (buyer_id, full_name, address, contact_number, payment_method, total_amount, status, order_date)
                          VALUES ('$buyer_id','$name','$address','$contact','$payment_method','$total','Pending', NOW())");
    $order_id = mysqli_insert_id($conn);

    foreach ($items as $it) {
        $pname = $it['name'];
        $size = $it['size_label'];
        $temp = $it['temperature'];
        $qty = $it['quantity'];
        $price = $it['price'];

        mysqli_query($conn, "INSERT INTO order_items (order_id, product_name, size_label, temperature, quantity, price_each)
                              VALUES ('$order_id','$pname','$size','$temp','$qty','$price')");

        // Reduce stock (not below 0)
        $new_stock = max(0, $it['stock'] - $qty);
        mysqli_query($conn, "UPDATE product_options SET stock='$new_stock' WHERE id='" . $it['option_id'] . "'");
    }

    // Clear cart and checkout session data
    mysqli_query($conn, "DELETE FROM cart_items WHERE buyer_id='$buyer_id'");
    unset($_SESSION['checkout_name'], $_SESSION['checkout_address'], $_SESSION['checkout_contact']);

    $_SESSION['last_order_id'] = $order_id;
    header('Location: payment.php?success=1');
    exit;
}

include 'includes/header.php';
?>
<h1>Payment</h1>

<?php if (isset($_GET['success'])): ?>
    <p style="color:green;">Order #<?php echo $_SESSION['last_order_id']; ?> placed successfully! (This is a class project - no real payment was processed.)</p>
    <p><a href="store.php">Continue Shopping</a></p>
<?php else: ?>
    <h3>Total to Pay: <?php echo format_price($total); ?></h3>
    <form method="POST" action="payment.php">
        Select Payment Method:<br>
        <input type="radio" name="payment_method" value="Cash on Delivery" checked> Cash on Delivery<br>
        <input type="radio" name="payment_method" value="GCash"> GCash (not yet integrated - simulation only)<br>
        <input type="radio" name="payment_method" value="Bank Transfer"> Bank Transfer (simulation only)<br><br>
        <input type="submit" value="Place Order">
    </form>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
