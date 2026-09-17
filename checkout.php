<?php

session_start();

require_once 'includes/db.php';


/* ================================
   LOGIN CHECK
   ================================ */

if (!isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit;
}


/* ================================
   CART CHECK
   ================================ */

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}


/* ================================
   GET CART PRODUCTS
   ================================ */

$product_ids = array_keys($_SESSION['cart']);

$placeholders = implode(
    ',',
    array_fill(0, count($product_ids), '?')
);

$stmt = $pdo->prepare("
    SELECT
        products.product_id,
        products.name,
        products.price,
        products.image,
        inventory.quantity AS stock
    FROM products

    INNER JOIN inventory
        ON products.product_id = inventory.product_id

    WHERE products.product_id IN ($placeholders)

    ORDER BY products.product_id
");

$stmt->execute($product_ids);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ================================
   CALCULATE TOTAL
   ================================ */

$total = 0;

foreach ($products as $product) {

    $quantity =
        $_SESSION['cart'][$product['product_id']];

    $total +=
        $product['price'] * $quantity;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Checkout | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<?php include 'includes/navbar.php'; ?>


<main>

<section class="checkout-section">


    <!-- ================================
         HEADING
         ================================ -->

    <div class="checkout-heading">

        <p class="section-label">
            CHECKOUT
        </p>

        <h1>
            Complete your <em>order.</em>
        </h1>

        <p>
            Review your order before placing it.
        </p>

    </div>


    <div class="checkout-layout">


        <!-- ================================
             ORDER ITEMS
             ================================ -->

        <div class="checkout-items">

            <p class="section-label">
                YOUR TEAS
            </p>

            <?php foreach ($products as $product): ?>

                <?php

                $quantity =
                    $_SESSION['cart']
                    [$product['product_id']];

                $subtotal =
                    $product['price'] * $quantity;

                ?>

                <div class="checkout-item">

                    <div class="checkout-item-image">

                        <img
                            src="assets/images/<?= htmlspecialchars(
                                $product['image']
                            ) ?>"
                            alt="<?= htmlspecialchars(
                                $product['name']
                            ) ?>"
                        >

                    </div>

                    <div class="checkout-item-info">

                        <h2>
                            <?= htmlspecialchars(
                                $product['name']
                            ) ?>
                        </h2>

                        <p>
                            <?= $quantity ?> ×
                            ₱<?= number_format(
                                $product['price'],
                                2
                            ) ?>
                        </p>

                    </div>

                    <strong>
                        ₱<?= number_format(
                            $subtotal,
                            2
                        ) ?>
                    </strong>

                </div>

            <?php endforeach; ?>

        </div>


        <!-- ================================
             ORDER SUMMARY
             ================================ -->

        <aside class="checkout-summary">

            <p class="section-label">
                ORDER SUMMARY
            </p>

            <h2>
                Almost <em>there.</em>
            </h2>


            <div class="checkout-summary-line">

                <span>
                    Customer
                </span>

                <strong>
                    <?= htmlspecialchars(
                        $_SESSION['user_name']
                    ) ?>
                </strong>

            </div>


            <div class="checkout-summary-line">

                <span>
                    Items
                </span>

                <strong>
                    <?= count($products) ?>
                </strong>

            </div>


            <div class="checkout-summary-total">

                <span>
                    Total
                </span>

                <strong>
                    ₱<?= number_format($total, 2) ?>
                </strong>

            </div>


            <form method="POST"
                  action="place_order.php">

                  <div class="form-group">
    <label for="payment_method">Payment Method</label>

    <select name="payment_method" id="payment_method" required>
        <option value="">Select a payment method</option>
        <option value="Cash on Delivery">Cash on Delivery</option>
        <option value="GCash">GCash</option>
        <option value="Bank Transfer">Bank Transfer</option>
    </select>
</div>

                <button
                    type="submit"
                    class="primary-button">
                    Place Order →
                </button>

            </form>


            <a href="cart.php"
               class="secondary-button">
                ← Back to Cart
            </a>

        </aside>


    </div>


</section>

</main>

</body>

</html>