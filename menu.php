<?php
require_once 'includes/db.php';

$stmt = $pdo->query("
    SELECT
        products.product_id,
        products.name,
        products.tea_type,
        products.price,
        products.description,
        products.image,
        categories.name AS category_name
    FROM products
    INNER JOIN categories
        ON products.category_id = categories.category_id
    ORDER BY products.product_id
");

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Menu | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<?php include 'includes/navbar.php'; ?>


<main>

    <section class="menu-page-section">

        <div class="menu-page-heading">

            <p class="section-label">
                OUR COLLECTION
            </p>

            <h1>
                Find your <em>perfect cup.</em>
            </h1>

            <p>
                Explore our collection of carefully selected
                loose-leaf teas, from delicate greens to
                rich black teas and fragrant botanical blends.
            </p>

        </div>


        <div class="menu-products">

            <?php foreach ($products as $product): ?>

                <article class="menu-product-card">

                    <div class="menu-product-image">

                        <img
                            src="assets/images/<?= htmlspecialchars($product['image']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>"
                        >

                    </div>


                    <div class="menu-product-info">

                        <div class="menu-product-top">

                            <div>

                                <p class="tea-type">
                                    <?= htmlspecialchars($product['tea_type']) ?>
                                </p>

                                <h2>
                                    <?= htmlspecialchars($product['name']) ?>
                                </h2>

                            </div>

                            <span class="tea-price">
                                ₱<?= number_format($product['price'], 2) ?>
                            </span>

                        </div>


                        <p class="menu-product-description">
                            <?= htmlspecialchars($product['description']) ?>
                        </p>


                        <p class="menu-product-category">
                            <?= htmlspecialchars($product['category_name']) ?>
                        </p>

        <form
    method="POST"
    action="cart.php"
    class="menu-add-cart-form"
>

            <input type="hidden" name="return_to" value="menu.php">

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
        class="primary-button">
        Add to Cart →
    </button>


</form>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    </section>

</main>


</body>

</html>