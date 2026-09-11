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
   INITIALIZE CART
   ================================ */

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


$message = "";
$message_type = "";


/* ================================
   ADD TO CART
   ================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['add_to_cart'])) {

    $product_id = (int) $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];

    if ($product_id <= 0 || $quantity <= 0) {

        $message = "Please select a valid quantity.";
        $message_type = "error";

    } else {

        $stmt = $pdo->prepare("
            SELECT
                products.product_id,
                products.name,
                products.price,
                inventory.quantity
            FROM products

            INNER JOIN inventory
                ON products.product_id = inventory.product_id

            WHERE products.product_id = ?
        ");

        $stmt->execute([$product_id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$product) {

            $message = "Product not found.";
            $message_type = "error";

        } elseif ($product['quantity'] <= 0) {

            $message = "This tea is currently out of stock.";
            $message_type = "error";

        } elseif ($quantity > $product['quantity']) {

            $message = "There is not enough stock available.";
            $message_type = "error";

        } else {

            $current_quantity =
                $_SESSION['cart'][$product_id] ?? 0;

            $new_quantity =
                $current_quantity + $quantity;


            if ($new_quantity > $product['quantity']) {

                $message =
                    "You cannot add more than the available stock.";

                $message_type = "error";

            } else {

                $_SESSION['cart'][$product_id] =
                    $new_quantity;

              $redirect_page = $_POST['return_to'] ?? 'cart.php';

$allowed_pages = [
    'index.php',
    'menu.php',
    'cart.php'
];

if (!in_array($redirect_page, $allowed_pages, true)) {
    $redirect_page = 'cart.php';
}

header("Location: " . $redirect_page);
exit;
            }
        }
    }
}


/* ================================
   REMOVE FROM CART
   ================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['remove_from_cart'])) {

    $product_id = (int) $_POST['product_id'];

    unset($_SESSION['cart'][$product_id]);

    header("Location: cart.php?removed=1");
    exit;
}


/* ================================
   UPDATE CART
   ================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['update_cart'])) {

    $product_id = (int) $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];


    if ($product_id > 0 && $quantity > 0) {

        $stmt = $pdo->prepare("
            SELECT inventory.quantity
            FROM inventory
            WHERE product_id = ?
        ");

        $stmt->execute([$product_id]);

        $stock = $stmt->fetchColumn();


        if ($stock !== false && $quantity <= $stock) {

            $_SESSION['cart'][$product_id] =
                $quantity;

        } else {

            $message =
                "The requested quantity is not available.";

            $message_type = "error";
        }

    } else {

        unset($_SESSION['cart'][$product_id]);
    }


    if ($message === "") {
        header("Location: cart.php?updated=1");
        exit;
    }
}


/* ================================
   SUCCESS MESSAGES
   ================================ */

if (isset($_GET['added'])) {

    $message = "Tea added to your cart.";
    $message_type = "success";
}

if (isset($_GET['removed'])) {

    $message = "Tea removed from your cart.";
    $message_type = "success";
}

if (isset($_GET['updated'])) {

    $message = "Cart updated successfully.";
    $message_type = "success";
}


/* ================================
   GET CART PRODUCTS
   ================================ */

$cart_products = [];
$cart_total = 0;


if (!empty($_SESSION['cart'])) {

    $product_ids =
        array_keys($_SESSION['cart']);

    $placeholders =
        implode(',', array_fill(
            0,
            count($product_ids),
            '?'
        ));


    $stmt = $pdo->prepare("
        SELECT
            products.product_id,
            products.name,
            products.tea_type,
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

    $cart_products =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach ($cart_products as $product) {

        $quantity =
            $_SESSION['cart'][$product['product_id']];

        $subtotal =
            $product['price'] * $quantity;

        $cart_total += $subtotal;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Cart | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<?php include 'includes/navbar.php'; ?>


<main>

<section class="cart-section">


    <!-- ================================
         HEADING
         ================================ -->

    <div class="cart-heading">

        <p class="section-label">
            YOUR CART
        </p>

        <h1>
            Your tea <em>selection.</em>
        </h1>

        <p>
            Review your selected teas before
            placing your order.
        </p>

    </div>


    <!-- ================================
         MESSAGE
         ================================ -->

    <?php if ($message !== ""): ?>

        <div class="account-message <?= $message_type ?>">

            <?= htmlspecialchars($message) ?>

        </div>

    <?php endif; ?>


    <?php if (empty($cart_products)): ?>


        <!-- EMPTY CART -->

        <div class="cart-empty">

            <p class="section-label">
                CART IS EMPTY
            </p>

            <h2>
                Nothing here <em>yet.</em>
            </h2>

            <p>
                Explore our teas and add something
                lovely to your selection.
            </p>

            <a href="menu.php"
               class="primary-button">
                Explore Teas →
            </a>

        </div>


    <?php else: ?>


        <!-- ================================
             CART ITEMS
             ================================ -->

        <div class="cart-layout">


            <div class="cart-items">

                <?php foreach ($cart_products as $product): ?>

                    <?php

                    $quantity =
                        $_SESSION['cart']
                        [$product['product_id']];

                    $subtotal =
                        $product['price'] * $quantity;

                    ?>

                    <article class="cart-item">


                        <div class="cart-item-image">

                            <img
                                src="assets/images/<?= htmlspecialchars(
                                    $product['image']
                                ) ?>"
                                alt="<?= htmlspecialchars(
                                    $product['name']
                                ) ?>"
                            >

                        </div>


                        <div class="cart-item-info">

                            <p class="tea-type">
                                <?= htmlspecialchars(
                                    $product['tea_type']
                                ) ?>
                            </p>

                            <h2>
                                <?= htmlspecialchars(
                                    $product['name']
                                ) ?>
                            </h2>

                            <p>
                                ₱<?= number_format(
                                    $product['price'],
                                    2
                                ) ?>
                                each
                            </p>


                            <form
                                method="POST"
                                action="cart.php"
                                class="cart-update-form"
                            >

                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?= (int) $product['product_id'] ?>"
                                >

                                <label
                                    for="quantity-<?= (int) $product['product_id'] ?>"
                                >
                                    Quantity
                                </label>

                                <input
                                    type="number"
                                    id="quantity-<?= (int) $product['product_id'] ?>"
                                    name="quantity"
                                    min="1"
                                    max="<?= (int) $product['stock'] ?>"
                                    value="<?= (int) $quantity ?>"
                                >

                                <button
                                    type="submit"
                                    name="update_cart"
                                >
                                    Update
                                </button>

                            </form>


                            <form
                                method="POST"
                                action="cart.php"
                            >

                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?= (int) $product['product_id'] ?>"
                                >

                                <button
                                    type="submit"
                                    name="remove_from_cart"
                                    class="cart-remove-button"
                                >
                                    Remove
                                </button>

                            </form>

                        </div>


                        <div class="cart-item-subtotal">

                            ₱<?= number_format(
                                $subtotal,
                                2
                            ) ?>

                        </div>


                    </article>

                <?php endforeach; ?>

            </div>


            <!-- ================================
                 CART SUMMARY
                 ================================ -->

            <aside class="cart-summary">

                <p class="section-label">
                    ORDER SUMMARY
                </p>

                <h2>
                    Your <em>order.</em>
                </h2>

                <div class="cart-summary-line">

                    <span>
                        Items
                    </span>

                    <span>
                        <?= count($cart_products) ?>
                    </span>

                </div>

                <div class="cart-summary-total">

                    <span>
                        Total
                    </span>

                    <strong>
                        ₱<?= number_format(
                            $cart_total,
                            2
                        ) ?>
                    </strong>

                </div>

                <a href="checkout.php"
                   class="primary-button">
                    Continue to Checkout →
                </a>

                <a href="menu.php"
                   class="secondary-button">
                    Continue Shopping
                </a>

            </aside>


        </div>


    <?php endif; ?>


</section>

</main>

</body>

</html>