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

// Delete a product (and its options + any cart items referencing those options)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_product') {
    $del_pid = clean_input($conn, $_POST['product_id']);

    $del_name_res = mysqli_query($conn, "SELECT name FROM products WHERE id='$del_pid'");
    $del_name = ($del_name_row = mysqli_fetch_assoc($del_name_res)) ? $del_name_row['name'] : "ID $del_pid";

    mysqli_query($conn, "DELETE ci FROM cart_items ci
                          INNER JOIN product_options po ON ci.product_option_id = po.id
                          WHERE po.product_id='$del_pid'");
    mysqli_query($conn, "DELETE FROM product_options WHERE product_id='$del_pid'");
    mysqli_query($conn, "DELETE FROM products WHERE id='$del_pid'");

    log_admin_action($conn, $_SESSION['admin_id'], $_SESSION['admin_username'], 'DELETE_PRODUCT',
        "Deleted product: $del_name (ID $del_pid)");

    $success = "Product deleted.";
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id");
$categories_list = [];
while ($c = mysqli_fetch_assoc($categories)) $categories_list[] = $c;

include 'includes/admin_header.php';
?>
<h1>Manage Stocks</h1>
<?php if ($success) echo "<p style='color:green;'>" . htmlspecialchars($success) . "</p>"; ?>

<h2>Add New Product</h2>
<form method="POST" action="manage_stocks.php" class="wide-form">
    <input type="hidden" name="action" value="add_product">

    <div class="wide-form-grid">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name">
        </div>
        <div class="form-group">
            <label>Description</label>
            <input type="text" name="description">
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category_id">
                <?php foreach ($categories_list as $c): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="wide-form-actions">
        <input type="submit" value="Add Product">
    </div>
</form>

<h2>Existing Products</h2>
<div class="admin-product-grid">
<?php
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY category_id, name");
while ($p = mysqli_fetch_assoc($products)):
    $pid = $p['id'];
    $opt_result = mysqli_query($conn, "SELECT * FROM product_options WHERE product_id='$pid' ORDER BY size_label, temperature");
    $opts = [];
    while ($o = mysqli_fetch_assoc($opt_result)) $opts[] = $o;
    $opts_json = htmlspecialchars(json_encode($opts), ENT_QUOTES, 'UTF-8');
?>
    <div class="admin-product-card">
        <div class="admin-product-card-header">
            <h3><?php echo htmlspecialchars($p['name']); ?></h3>
            <?php if ($p['is_special']): ?><span class="special-tag">SPECIAL</span><?php endif; ?>
        </div>
        <div class="admin-product-card-image">
            <?php if (!empty($p['image'])): ?>
                <img src="../assets/products/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-photo">
            <?php else: ?>
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" class="cup-icon">
                    <path d="M20 35 H70 V70 Q70 85 45 85 Q20 85 20 70 Z" fill="#e8c39e" stroke="#000" stroke-width="3"/>
                    <ellipse cx="45" cy="35" rx="25" ry="7" fill="#f4d9b8" stroke="#000" stroke-width="3"/>
                    <path d="M70 42 Q88 42 88 55 Q88 68 70 65" fill="none" stroke="#000" stroke-width="3"/>
                    <path d="M32 15 Q28 22 34 27 Q40 32 36 39" fill="none" stroke="#050C9C" stroke-width="3" stroke-linecap="round"/>
                    <path d="M46 12 Q42 19 48 24 Q54 29 50 36" fill="none" stroke="#050C9C" stroke-width="3" stroke-linecap="round"/>
                </svg>
            <?php endif; ?>
        </div>
        <p class="product-desc"><?php echo htmlspecialchars($p['description']); ?></p>
        <div class="admin-product-card-actions">
            <button type="button" class="btn-update-stock"
                data-product-name="<?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?>"
                data-options='<?php echo $opts_json; ?>'>
                UPDATE
            </button>
            <form method="POST" action="manage_stocks.php" class="delete-product-form"
                  onsubmit="return confirm('Delete &quot;<?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?>&quot;? This cannot be undone.');">
                <input type="hidden" name="action" value="delete_product">
                <input type="hidden" name="product_id" value="<?php echo $pid; ?>">
                <button type="submit" class="btn-delete-product">DELETE</button>
            </form>
        </div>
    </div>
<?php endwhile; ?>
</div>

<!-- ========== UPDATE STOCK MODAL ========== -->
<div class="modal-overlay" id="stockModalOverlay">
    <div class="modal-box">
        <button type="button" class="modal-close" id="stockModalClose">&times;</button>
        <h2 id="stockModalProductName">Product</h2>

        <form method="POST" action="manage_stocks.php" id="stockModalForm">
            <div class="modal-section">
                <label class="modal-label">Size</label>
                <select id="stockSizeSelect" class="modal-select"></select>
            </div>

            <div class="modal-section">
                <label class="modal-label">Temperature</label>
                <select id="stockTempSelect" class="modal-select"></select>
            </div>

            <div class="modal-section">
                <label class="modal-label">Price (₱)</label>
                <input type="text" name="price" id="stockPriceInput">
            </div>

            <div class="modal-section">
                <label class="modal-label">Stock</label>
                <input type="text" name="stock" id="stockStockInput">
            </div>

            <input type="hidden" name="option_id" id="stockOptionId">
            <button type="submit" class="btn-primary modal-submit">Save Changes</button>
        </form>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('stockModalOverlay');
    var closeBtn = document.getElementById('stockModalClose');
    var nameEl = document.getElementById('stockModalProductName');
    var sizeSelect = document.getElementById('stockSizeSelect');
    var tempSelect = document.getElementById('stockTempSelect');
    var priceInput = document.getElementById('stockPriceInput');
    var stockInput = document.getElementById('stockStockInput');
    var optionIdInput = document.getElementById('stockOptionId');

    var currentOptions = [];

    function uniqueValues(arr, key) {
        var seen = [];
        arr.forEach(function (o) {
            if (seen.indexOf(o[key]) === -1) seen.push(o[key]);
        });
        return seen;
    }

    function findOption(size, temp) {
        return currentOptions.find(function (o) {
            return o.size_label === size && o.temperature === temp;
        });
    }

    function fillTempOptionsForSize(size) {
        var temps = uniqueValues(currentOptions.filter(function (o) { return o.size_label === size; }), 'temperature');
        tempSelect.innerHTML = '';
        temps.forEach(function (t) {
            var opt = document.createElement('option');
            opt.value = t;
            opt.textContent = t;
            tempSelect.appendChild(opt);
        });
    }

    function applySelection() {
        var size = sizeSelect.value;
        var temp = tempSelect.value;
        var match = findOption(size, temp);
        if (match) {
            priceInput.value = match.price;
            stockInput.value = match.stock;
            optionIdInput.value = match.id;
        }
    }

    function openModal(name, options) {
        currentOptions = options;
        nameEl.textContent = name;

        var sizes = uniqueValues(options, 'size_label');
        sizeSelect.innerHTML = '';
        sizes.forEach(function (s) {
            var opt = document.createElement('option');
            opt.value = s;
            opt.textContent = s;
            sizeSelect.appendChild(opt);
        });

        fillTempOptionsForSize(sizes[0]);
        applySelection();

        overlay.classList.add('open');
    }

    function closeModal() {
        overlay.classList.remove('open');
    }

    document.querySelectorAll('.btn-update-stock').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var name = btn.getAttribute('data-product-name');
            var options = JSON.parse(btn.getAttribute('data-options'));
            openModal(name, options);
        });
    });

    sizeSelect.addEventListener('change', function () {
        fillTempOptionsForSize(sizeSelect.value);
        applySelection();
    });

    tempSelect.addEventListener('change', applySelection);

    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal();
    });
})();
</script>

<?php include 'includes/admin_footer.php'; ?>