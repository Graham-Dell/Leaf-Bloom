<?php
$isAdminPage = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdminPage ? '../' : '';
?>

<header class="site-header">

    <nav class="navbar">

        <!-- ==================== LOGO ==================== -->

        <a href="<?= $basePath ?>index.php" class="brand">
            Leaf &amp; Bloom
        </a>


        <!-- ==================== MOBILE MENU BUTTON ==================== -->

        <button
            class="nav-toggle"
            id="navToggle"
            type="button"
            aria-label="Open navigation"
            aria-expanded="false"
            aria-controls="mainNav">

            <span></span>
            <span></span>
            <span></span>

        </button>


        <!-- ==================== NAVIGATION ==================== -->

        <div class="nav-right" id="mainNav">

            <div class="nav-links">

    <a href="<?= $basePath ?>menu.php">
        Menu
    </a>

    <a href="<?= $basePath ?>index.php#philosophy">
        About
    </a>

    <a href="<?= $basePath ?>inventory.php">
        Inventory
    </a>

    <a href="<?= $basePath ?>contact.php">
        Contact
    </a>

    <a href="<?= $basePath ?>account.php">
        Account
    </a>

    <a href="<?= $basePath ?>cart.php">
        Cart
    </a>

</div>

<a href="<?= $basePath ?>index.php#teas"
   class="shop-button">
    Shop Now
</a>
        </div>

    </nav>

</header>