<?php

session_start();

require_once 'includes/db.php';

$stmt = $pdo->query("
    SELECT
        product_id,
        name,
        tea_type,
        price,
        description,
        image
    FROM products
    ORDER BY product_id
");

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Leaf & Bloom</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- ==================== NAVBAR ==================== -->
    <?php include 'includes/navbar.php'; ?>


    <!-- ==================== HERO SECTION ==================== -->

<section class="hero">

    <!-- LEFT SIDE -->
    <div class="hero-content">

        <p class="eyebrow">
            ARTISAN LOOSE LEAF · EST. 2019
        </p>

        <h1>
            Where<br>
            every leaf<br>
            <em>tells a story</em>
        </h1>

        <p class="hero-description">
            We source rare and seasonal teas directly from
            small-estate gardens across Asia and Europe —
            then bring them to your cup with care.
        </p>

        <div class="hero-buttons">

    <!-- Explore Teas = scroll to Leaves worth savouring -->
    <a href="#teas" class="primary-button">
        Explore Teas →
    </a>

    <!-- Our Story = scroll to Our Philosophy -->
    <a href="#philosophy" class="secondary-button">
        Our Story
    </a>

</div>

        <!-- HERO STATISTICS -->
        <div class="hero-stats">

            <div class="stat">
                <strong>60+</strong>
                <span>ESTATE ORIGINS</span>
            </div>

            <div class="stat">
                <strong>18</strong>
                <span>COUNTRIES</span>
            </div>

            <div class="stat">
                <strong>4.9★</strong>
                <span>AVERAGE RATING</span>
            </div>

        </div>

    </div>


    <!-- RIGHT SIDE / IMAGE -->
    <div class="hero-image">

    <img
        src="assets/images/logo.jpg"
        alt="Tea being poured into small cups">

    <span class="image-caption">
        GYOKURO · UJI, JAPAN
    </span>

</div>

</section>


<!-- ==================== TEA SELECTION ==================== -->
<!-- 📍 LEAVES WORTH SAVOURING START -->

<section id="teas" class="tea-section">

    <!-- Section heading -->
    <div class="tea-heading">

        <div>
            <p class="section-label">
                OUR COLLECTION
            </p>

            <h2>
                Leaves worth <em>savouring.</em>
            </h2>
        </div>

        <p class="tea-intro">
            From delicate greens to bold black teas,
            every leaf has a character of its own.
        </p>

    </div>


    <!-- Tea cards -->
    <div class="tea-grid">


        <?php foreach ($products as $product): ?>

    <article class="tea-card">

    <div class="tea-image">
        <img
            src="assets/images/<?= htmlspecialchars($product['image']) ?>"
            alt="<?= htmlspecialchars($product['name']) ?>"
        >
    </div>


    <div class="tea-info">

        <div>

            <p class="tea-type">
                <?= htmlspecialchars($product['tea_type']) ?>
            </p>

            <h3>
                <?= htmlspecialchars($product['name']) ?>
            </h3>

        </div>

        <span class="tea-price">
            ₱<?= number_format($product['price'], 2) ?>
        </span>

    </div>


    <p class="tea-description">
        <?= htmlspecialchars($product['description']) ?>
    </p>


    <!-- ================================
         ADD TO CART
         ================================ -->

    <form
    method="POST"
    action="cart.php"
    class="tea-add-cart-form"
>

            <input type="hidden" name="return_to" value="index.php">


        <input
            type="hidden"
            name="product_id"
            value="<?= (int) $product['product_id'] ?>"
        >

        <input
            type="number"
            name="quantity"
            value="1"
            min="1"
            max="99"
            aria-label="Quantity"
        >

        <button
            type="submit"
            name="add_to_cart"
            class="primary-button"
        >
            Add to Cart →
        </button>


    </form>

</article>

<?php endforeach; ?>


    </div>

</section>

<!-- 📍 LEAVES WORTH SAVOURING END -->


<!-- 📍 OUR PHILOSOPHY START -->

<section id="philosophy" class="philosophy-section">

    <div class="philosophy-content">

        <p class="section-label">
            OUR PHILOSOPHY
        </p>

        <h2>
            Tea is more than<br>
            <em>a drink.</em>
        </h2>

        <p class="philosophy-text">
            We believe the best tea begins with respect —
            for the land, the people who cultivate it,
            and the traditions that have carried each leaf
            through generations.
        </p>

        <p class="philosophy-text">
            That is why we work closely with small-estate
            growers and choose teas for their character,
            seasonality, and story.
        </p>

        <a href="about.php" class="secondary-button">
            Discover Our Story →
        </a>

    </div>

    <div class="philosophy-image">

    <img
        src="assets/images/philosophy.webp"
        alt="Tea leaves and botanical ingredients">

    <span class="image-caption">
        HAND-PICKED · SMALL ESTATE
    </span>

</div>

</section>

<!-- 📍 OUR PHILOSOPHY END -->

    <!-- ==================== HOW WE WORK ==================== -->
<!-- 📍 HOW WE WORK START -->

<section class="how-we-work">

    <div class="how-header">

        <p class="section-label">
            HOW WE WORK
        </p>

        <h2>
            From garden<br>
            <em>to your cup.</em>
        </h2>

        <p class="how-intro">
            Every Leaf &amp; Bloom tea follows a simple journey —
            carefully sourced, thoughtfully prepared, and made
            to be savoured.
        </p>

    </div>


    <div class="process-grid">

        <!-- STEP 01 -->
        <div class="process-step">

            <span class="process-number">
                01
            </span>

            <h3>
                We Source
            </h3>

            <p>
                We partner with small-estate growers and
                seek out exceptional leaves from trusted
                tea-growing regions.
            </p>

        </div>


        <!-- STEP 02 -->
        <div class="process-step">

            <span class="process-number">
                02
            </span>

            <h3>
                We Select
            </h3>

            <p>
                Each tea is chosen for its distinctive
                character, aroma, season, and quality.
            </p>

        </div>


        <!-- STEP 03 -->
        <div class="process-step">

            <span class="process-number">
                03
            </span>

            <h3>
                We Prepare
            </h3>

            <p>
                We handle every blend with care so the
                natural character of each leaf can shine.
            </p>

        </div>


        <!-- STEP 04 -->
        <div class="process-step">

            <span class="process-number">
                04
            </span>

            <h3>
                You Savour
            </h3>

            <p>
                Finally, the journey reaches your cup —
                ready for a quiet moment worth enjoying.
            </p>

        </div>

    </div>

</section>

<!-- 📍 HOW WE WORK END -->


    <!-- ==================== MENU ==================== -->
<!-- 📍 THE MENU START -->

<section class="menu-preview">

    <div class="menu-preview-header">

        <div>
            <p class="section-label">
                THE MENU
            </p>

            <h2>
                Find your<br>
                <em>perfect cup.</em>
            </h2>
        </div>

        <p class="menu-intro">
            Explore our collection of carefully selected
            loose-leaf teas, from bright green teas to
            rich oolongs and fragrant botanical blends.
        </p>

    </div>


    <div class="menu-categories">

        <!-- GREEN & WHITE -->
        <a href="menu.php" class="menu-category">

           <div class="menu-category-image">

    <img
        src="assets/images/green-white.jpg"
        alt="Green and White tea">

</div>

            <div class="menu-category-info">

                <h3>
                    Green &amp; White
                </h3>

                <span>
                    Explore collection →
                </span>

            </div>

        </a>


        <!-- BLACK & OOLONG -->
        <a href="menu.php" class="menu-category">

            <div class="menu-category-image">

    <img
        src="assets/images/black-oolong.jpg"
        alt="Black and Oolong tea">

</div>

            <div class="menu-category-info">

                <h3>
                    Black &amp; Oolong
                </h3>

                <span>
                    Explore collection →
                </span>

            </div>

        </a>


        <!-- BOTANICAL BLENDS -->
        <a href="menu.php" class="menu-category">

            <div class="menu-category-image">

    <img
        src="assets/images/botanical-blends.webp"
        alt="Botanical tea blends">

</div>

            <div class="menu-category-info">

                <h3>
                    Botanical Blends
                </h3>

                <span>
                    Explore collection →
                </span>

            </div>

        </a>

    </div>


    <div class="menu-button-wrapper">

        <a href="menu.php" class="primary-button">
            View Full Menu →
        </a>

    </div>

</section>

<!-- 📍 THE MENU END -->

    <!-- ==================== NEWSLETTER ==================== -->
<!-- 📍 NEWSLETTER START -->

<section id="newsletter" class="newsletter-section">

    <div class="newsletter-content">

        <p class="section-label">
            STAY IN THE LOOP
        </p>

        <h2>
            A little tea<br>
            <em>in your inbox.</em>
        </h2>

        <p class="newsletter-description">
            Join our tea journal for new arrivals, seasonal
            selections, brewing notes, and occasional stories
            from the gardens we source from.
        </p>

        <form class="newsletter-form" action="subscribe.php" method="post">

            <input
                type="email"
                name="email"
                placeholder="Your email address"
                aria-label="Your email address"
                required
            >

            <button type="submit">
                Subscribe →
            </button>

        </form>

        <p class="newsletter-note">
            No clutter. Just good tea.
        </p>

        <?php if (isset($_SESSION['newsletter_success'])): ?>

    <p class="newsletter-message success">
        <?= htmlspecialchars($_SESSION['newsletter_success']) ?>
    </p>

    <?php unset($_SESSION['newsletter_success']); ?>

<?php endif; ?>


<?php if (isset($_SESSION['newsletter_error'])): ?>

    <p class="newsletter-message error">
        <?= htmlspecialchars($_SESSION['newsletter_error']) ?>
    </p>

    <?php unset($_SESSION['newsletter_error']); ?>

<?php endif; ?>

    </div>

</section>

<!-- 📍 NEWSLETTER END -->


    <!-- ==================== FOOTER ==================== -->
<!-- 📍 FOOTER START -->

<footer class="site-footer">

    <div class="footer-main">

        <!-- BRAND -->
        <div class="footer-brand">

            <a href="index.php" class="footer-logo">
                Leaf &amp; Bloom
            </a>

            <p>
                Thoughtfully sourced tea for
                slower, sweeter moments.
            </p>

        </div>


        <!-- EXPLORE -->
        <div class="footer-column">

            <h3>
                Explore
            </h3>

            <a href="menu.php">Menu</a>

            <a href="#philosophy">About</a>

            <a href="inventory.php">Inventory</a>

        </div>


        <!-- HELP -->
        <div class="footer-column">

            <h3>
                Help
            </h3>

            <a href="contact.php">Contact</a>

            <a href="account.php">Account</a>

            <a href="#teas">Shop Now</a>

        </div>


        <!-- SOCIAL -->
        <div class="footer-column">

            <h3>
                Follow
            </h3>

            <a href="#" aria-label="Instagram">
                Instagram
            </a>

            <a href="#" aria-label="Facebook">
                Facebook
            </a>

        </div>

    </div>


    <!-- FOOTER BOTTOM -->

    <div class="footer-bottom">

        <span>
            © 2026 Leaf &amp; Bloom
        </span>

        <span>
            ARTISAN LOOSE LEAF · EST. 2019
        </span>

    </div>

</footer>

<!-- 📍 FOOTER END -->


    <script src="assets/js/nav.js" defer></script>

</body>
</html>