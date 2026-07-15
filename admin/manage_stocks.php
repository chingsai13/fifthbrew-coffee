<?php
require_once 'includes/admin_init.php';
require_admin_login();
$page_title = "Manage Stocks";
$success = "";

// Update price/stock for one product option
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['option_id'])) {
    $option_id = clean_input($conn, $_POST['option_id']);
    $price = clean_input($conn, $_POST['price']);
    $stock = clean_input($conn, $_POST['stock']);

    mysqli_query($conn, "UPDATE product_options SET price='$price', stock='$stock' WHERE id='$option_id'");

    log_admin_action($conn, $_SESSION['admin_id'], $_SESSION['admin_username'], 'UPDATE_STOCK',
        "Updated option ID $option_id to price=$price, stock=$stock");

    $success = "Updated successfully.";
}

// Add a brand new product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_product') {
    $name = clean_input($conn, $_POST['name']);
    $description = clean_input($conn, $_POST['description']);
    $category_id = clean_input($conn, $_POST['category_id']);

    mysqli_query($conn, "INSERT INTO products (category_id, name, description, is_special, created_at)
                          VALUES ('$category_id','$name','$description', 0, NOW())");
    $new_id = mysqli_insert_id($conn);

    log_admin_action($conn, $_SESSION['admin_id'], $_SESSION['admin_username'], 'ADD_PRODUCT', "Added product: $name");

    // Add one default option so it's editable right away
    mysqli_query($conn, "INSERT INTO product_options (product_id, size_label, temperature, price, stock)
                          VALUES ('$new_id','Lupa (12oz)','Hot', 100, 0)");

    $success = "Product added. You can now set its size/price/stock options below.";
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id");

include 'includes/admin_header.php';
?>
<h1>Manage Stocks</h1>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

<h2>Add New Product</h2>
<form method="POST" action="manage_stocks.php">
    <input type="hidden" name="action" value="add_product">
    Name: <input type="text" name="name"><br><br>
    Description: <input type="text" name="description"><br><br>
    Category:
    <select name="category_id">
        <?php
        mysqli_data_seek($categories, 0);
        while ($c = mysqli_fetch_assoc($categories)): ?>
            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
        <?php endwhile; ?>
    </select><br><br>
    <input type="submit" value="Add Product">
</form>

<h2>Existing Products</h2>
<?php
mysqli_data_seek($categories, 0);
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY category_id, name");
while ($p = mysqli_fetch_assoc($products)):
    $pid = $p['id'];
    $options = mysqli_query($conn, "SELECT * FROM product_options WHERE product_id='$pid' ORDER BY size_label");
?>
    <h3><?php echo htmlspecialchars($p['name']); ?></h3>
    <div>
        <strong>Size</strong> | <strong>Temp</strong> | <strong>Price</strong> | <strong>Stock</strong>
        <?php while ($opt = mysqli_fetch_assoc($options)): ?>
            <form method="POST" action="manage_stocks.php">
                <?php echo htmlspecialchars($opt['size_label']); ?> |
                <?php echo htmlspecialchars($opt['temperature']); ?> |
                <input type="text" name="price" value="<?php echo $opt['price']; ?>" style="width:70px;"> |
                <input type="text" name="stock" value="<?php echo $opt['stock']; ?>" style="width:50px;">
                <input type="hidden" name="option_id" value="<?php echo $opt['id']; ?>">
                <input type="submit" value="Save">
            </form>
        <?php endwhile; ?>
    </div>
<?php endwhile; ?>

<?php include 'includes/admin_footer.php'; ?>
