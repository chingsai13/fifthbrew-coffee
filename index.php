<?php
require_once 'includes/init.php';
$page_title = "Home";
include 'includes/header.php';
?>

<!-- ========== HERO ========== -->
<section class="hero">
    <div class="hero-text">
        <h1>This is 5th Brew.</h1>
        <p>Filipino-inspired coffee, tea, and specialty lattes.</p>
        <a href="store.php" class="hero-button">Order Now</a>
    </div>

    <div class="hero-image">
        <img src="assets/Logo.png" alt="5th Brew">
    </div>
</section>

<!-- ========== CATEGORIES ========== -->
<section class="categories">
    <h2>Shop by Category</h2>
    <div class="category-grid">
        <a href="store.php" class="category-card">
            <img src="assets/Coffee1.png" alt="Coffee">
            <h3>Coffee</h3>
        </a>
        <a href="store.php" class="category-card">
            <img src="assets/mockup.png" alt="Non-Coffee">
            <h3>Non-Coffee</h3>
        </a>
        <a href="store.php" class="category-card">
            <img src="assets/garlicbread1.png" alt="Pastries">
            <h3>Pastries</h3>
        </a>
    </div>
</section>

<!-- ========== ABOUT TEASER ========== -->
<section class="about-teaser">
    <div class="about-teaser-content">
        <h2>Who We Are</h2>
        <p>5th Brew was started by five friends who wanted to bring good, honest coffee to everyone. From our first cup to yours, we're all about quality beans, simple recipes, and a warm place to slow down for a bit.</p>
        <a href="about.php" class="about-teaser-link">Learn More About Us</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>