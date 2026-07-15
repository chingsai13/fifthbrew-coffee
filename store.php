<?php
require_once 'includes/init.php';
$page_title = "Store";

$msg = "";
// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_option_id'])) {
    if (!buyer_is_logged_in()) {
        header('Location: login.php');
        exit;
    }
    $option_id = clean_input($conn, $_POST['product_option_id']);
    $qty = (int) $_POST['quantity'];
    if ($qty < 1) $qty = 1;
    $buyer_id = $_SESSION['buyer_id'];

    // If already in cart, update quantity instead of duplicating
    $check = mysqli_query($conn, "SELECT id, quantity FROM cart_items WHERE buyer_id='$buyer_id' AND product_option_id='$option_id'");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $new_qty = $row['quantity'] + $qty;
        mysqli_query($conn, "UPDATE cart_items SET quantity='$new_qty' WHERE id='" . $row['id'] . "'");
    } else {
        mysqli_query($conn, "INSERT INTO cart_items (buyer_id, product_option_id, quantity, added_at)
                              VALUES ('$buyer_id','$option_id','$qty', NOW())");
    }
    $msg = "Added to cart!";
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id");

include 'includes/header.php';
?>
<h1>Our Menu</h1>
<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>

<?php while ($cat = mysqli_fetch_assoc($categories)):
    $cat_id = $cat['id'];
    $products = mysqli_query($conn, "SELECT * FROM products WHERE category_id='$cat_id' ORDER BY name");
?>
    <h2><?php echo htmlspecialchars($cat['name']); ?></h2>

    <?php while ($p = mysqli_fetch_assoc($products)):
        $pid = $p['id'];
        $options = mysqli_query($conn, "SELECT * FROM product_options WHERE product_id='$pid' ORDER BY price");
    ?>
        <div>
            <h3><?php echo htmlspecialchars($p['name']); ?> <?php echo $p['is_special'] ? '(SPECIAL)' : ''; ?></h3>
            <p><?php echo htmlspecialchars($p['description']); ?></p>

            <form method="POST" action="store.php">
                <select name="product_option_id">
                    <?php while ($opt = mysqli_fetch_assoc($options)): ?>
                        <option value="<?php echo $opt['id']; ?>">
                            <?php echo htmlspecialchars($opt['size_label']) . ' - ' . htmlspecialchars($opt['temperature']) . ' - ' . format_price($opt['price']); ?>
                            <?php echo $opt['stock'] <= 0 ? ' (Out of stock)' : ''; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                Qty: <input type="number" name="quantity" value="1" min="1" style="width:50px;">
                <input type="submit" value="Add to Cart">
            </form>
        </div>
        <hr>
    <?php endwhile; ?>
<?php endwhile; ?>

<?php include 'includes/footer.php'; ?>
