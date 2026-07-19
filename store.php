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
$categories_list = [];
while ($c = mysqli_fetch_assoc($categories)) $categories_list[] = $c;

include 'includes/header.php';
?>

<!-- ========== STORE BANNER ========== -->
<section class="store-banner">
    <div class="store-banner-overlay"></div>
    <img class="store-banner-logo" src="assets/logo.jpg" alt="5th Brew">
</section>

<div class="store-layout">
    <!-- ========== SIDEBAR ========== -->
    <aside class="store-sidebar">
        <h2 class="sidebar-title">MENU</h2>
            <ul class="category-list">
                <li><a href="javascript:void(0)" class="category-link active" data-cat="all">All</a></li>
                <?php foreach ($categories_list as $c): ?>
                    <li><a href="javascript:void(0)" class="category-link" data-cat="cat-<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></a></li>
                <?php endforeach; ?>
            </ul>
    </aside>

    <!-- ========== MAIN MENU ========== -->
    <div class="store-main">
        <div class="store-search-row">
            <input type="text" id="drinkSearch" class="search-bar" placeholder="Search Our Drinks">
        </div>

        <?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>

        <?php foreach ($categories_list as $cat):
            $cat_id = $cat['id'];
            $products = mysqli_query($conn, "SELECT * FROM products WHERE category_id='$cat_id' ORDER BY name");
        ?>
            <div class="product-category-block" data-cat-block="cat-<?php echo $cat_id; ?>">
                <h2 class="category-heading"><?php echo htmlspecialchars($cat['name']); ?></h2>

                <div class="product-grid">
                    <?php while ($p = mysqli_fetch_assoc($products)):
                        $pid = $p['id'];
                        $opt_result = mysqli_query($conn, "SELECT * FROM product_options WHERE product_id='$pid' ORDER BY price");
                        $opts = [];
                        while ($o = mysqli_fetch_assoc($opt_result)) $opts[] = $o;
                        $opts_json = htmlspecialchars(json_encode($opts), ENT_QUOTES, 'UTF-8');
                    ?>
                        <div class="product-card" data-name="<?php echo strtolower(htmlspecialchars($p['name'])); ?>">
                            <div class="product-card-header">
                                <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                                <?php if ($p['is_special']): ?><span class="special-tag">SPECIAL</span><?php endif; ?>
                            </div>
                            <div class="product-card-image">
                                <?php if (!empty($p['image'])): ?>
                                    <img src="assets/products/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-photo">
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
                            <button type="button" class="btn-add-cart"
                                data-product-id="<?php echo $pid; ?>"
                                data-product-name="<?php echo htmlspecialchars($p['name'], ENT_QUOTES); ?>"
                                data-options='<?php echo $opts_json; ?>'>
                                ADD TO CART
                            </button>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ========== ADD TO CART MODAL ========== -->
<div class="modal-overlay" id="cartModalOverlay">
    <div class="modal-box">
        <button type="button" class="modal-close" id="modalClose">&times;</button>
        <h2 id="modalProductName">Product</h2>

        <form method="POST" action="store.php" id="modalForm">
            <div class="modal-section">
                <label class="modal-label">Size &amp; Temperature</label>
                <div id="modalOptions" class="modal-options"></div>
            </div>

            <div class="modal-section modal-qty-row">
                <label class="modal-label">Quantity</label>
                <div class="qty-stepper">
                    <button type="button" id="qtyMinus" class="qty-btn">&minus;</button>
                    <input type="number" name="quantity" id="modalQty" value="1" min="1">
                    <button type="button" id="qtyPlus" class="qty-btn">+</button>
                </div>
            </div>

            <div class="modal-price-row">
                <span>Price</span>
                <span id="modalPrice">₱0.00</span>
            </div>

            <input type="hidden" name="product_option_id" id="modalOptionId">
            <button type="submit" class="btn-primary modal-submit" id="modalSubmit" disabled>Add to Cart</button>
        </form>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('cartModalOverlay');
    var closeBtn = document.getElementById('modalClose');
    var nameEl = document.getElementById('modalProductName');
    var optionsEl = document.getElementById('modalOptions');
    var optionIdInput = document.getElementById('modalOptionId');
    var priceEl = document.getElementById('modalPrice');
    var submitBtn = document.getElementById('modalSubmit');
    var qtyInput = document.getElementById('modalQty');

    var selectedPrice = 0;

    function formatPrice(n) {
        return '₱' + Number(n).toFixed(2);
    }

    function openModal(name, options) {
        nameEl.textContent = name;
        optionsEl.innerHTML = '';
        optionIdInput.value = '';
        submitBtn.disabled = true;
        priceEl.textContent = '₱0.00';
        qtyInput.value = 1;

        options.forEach(function (opt, idx) {
            var out = opt.stock <= 0;
            var pill = document.createElement('button');
            pill.type = 'button';
            pill.className = 'option-pill' + (out ? ' out-of-stock' : '');
            pill.disabled = out;
            pill.innerHTML = '<span class="option-size">' + opt.size_label + ' &middot; ' + opt.temperature + '</span>' +
                              '<span class="option-price">' + formatPrice(opt.price) + (out ? ' (Out of stock)' : '') + '</span>';

            pill.addEventListener('click', function () {
                var siblings = optionsEl.querySelectorAll('.option-pill');
                siblings.forEach(function (s) { s.classList.remove('selected'); });
                pill.classList.add('selected');
                optionIdInput.value = opt.id;
                selectedPrice = opt.price;
                priceEl.textContent = formatPrice(selectedPrice * (parseInt(qtyInput.value) || 1));
                submitBtn.disabled = false;
            });

            optionsEl.appendChild(pill);
        });

        overlay.classList.add('open');
    }

    function closeModal() {
        overlay.classList.remove('open');
    }

    document.querySelectorAll('.btn-add-cart').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var name = btn.getAttribute('data-product-name');
            var options = JSON.parse(btn.getAttribute('data-options'));
            openModal(name, options);
        });
    });

    closeBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal();
    });

    document.getElementById('qtyPlus').addEventListener('click', function () {
        qtyInput.value = (parseInt(qtyInput.value) || 1) + 1;
        priceEl.textContent = formatPrice(selectedPrice * qtyInput.value);
    });
    document.getElementById('qtyMinus').addEventListener('click', function () {
        var v = (parseInt(qtyInput.value) || 1) - 1;
        if (v < 1) v = 1;
        qtyInput.value = v;
        priceEl.textContent = formatPrice(selectedPrice * qtyInput.value);
    });
    qtyInput.addEventListener('input', function () {
        var v = parseInt(qtyInput.value) || 1;
        if (v < 1) v = 1;
        priceEl.textContent = formatPrice(selectedPrice * v);
    });

    // Category sidebar filter
    document.querySelectorAll('.category-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('.category-link').forEach(function (l) { l.classList.remove('active'); });
            link.classList.add('active');
            var cat = link.getAttribute('data-cat');
            document.querySelectorAll('.product-category-block').forEach(function (block) {
                if (cat === 'all' || block.getAttribute('data-cat-block') === cat) {
                    block.style.display = '';
                } else {
                    block.style.display = 'none';
                }
            });
        });
    });

    // Search filter
    document.getElementById('drinkSearch').addEventListener('input', function () {
        var q = this.value.trim().toLowerCase();
        document.querySelectorAll('.product-card').forEach(function (card) {
            card.style.display = card.getAttribute('data-name').indexOf(q) !== -1 ? '' : 'none';
        });
    });
})();
</script>

<?php include 'includes/footer.php'; ?>