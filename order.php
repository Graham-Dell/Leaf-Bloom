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


$message = "";
$message_type = "";


/* ================================
   PLACE ORDER
   ================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['place_order'])) {

    $product_id = (int) $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];

    if ($product_id <= 0 || $quantity <= 0) {

        $message = "Please select a valid tea and quantity.";
        $message_type = "error";

    } else {

        try {

            $pdo->beginTransaction();


            /* ================================
               GET PRODUCT + STOCK
               ================================ */

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

                FOR UPDATE
            ");

            $stmt->execute([$product_id]);

            $product = $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$product) {

                throw new Exception("Product not found.");

            }


            /* ================================
               CHECK STOCK
               ================================ */

            if ($quantity > $product['quantity']) {

                throw new Exception(
                    "Not enough stock available."
                );
            }


            /* ================================
               CALCULATE TOTAL
               ================================ */

            $total = $product['price'] * $quantity;


            /* ================================
               CREATE ORDER
               ================================ */

            $stmt = $pdo->prepare("
                INSERT INTO orders
                (user_id, total_amount, status)
                VALUES (?, ?, 'Pending')
            ");

            $stmt->execute([
                $_SESSION['user_id'],
                $total
            ]);


            $order_id = $pdo->lastInsertId();


            /* ================================
               CREATE ORDER ITEM
               ================================ */

            $stmt = $pdo->prepare("
                INSERT INTO order_items
                (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $order_id,
                $product_id,
                $quantity,
                $product['price']
            ]);


            /* ================================
               REDUCE INVENTORY
               ================================ */

            $new_quantity =
                $product['quantity'] - $quantity;

            $stmt = $pdo->prepare("
                UPDATE inventory
                SET quantity = ?
                WHERE product_id = ?
            ");

            $stmt->execute([
                $new_quantity,
                $product_id
            ]);


            $pdo->commit();


            header(
                "Location: order.php?success=1&order_id="
                . $order_id
            );

            exit;


        } catch (Exception $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $message = $e->getMessage();
            $message_type = "error";
        }
    }
}


/* ================================
   SUCCESS MESSAGE
   ================================ */

if (isset($_GET['success'])) {

    $message =
        "Order #"
        . (int) $_GET['order_id']
        . " placed successfully!";

    $message_type = "success";
}


/* ================================
   GET PRODUCTS
   ================================ */

$stmt = $pdo->query("
    SELECT
        products.product_id,
        products.name,
        products.tea_type,
        products.price,
        products.description,
        inventory.quantity
    FROM products

    INNER JOIN inventory
        ON products.product_id = inventory.product_id

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

    <title>Order Tea | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<?php include 'includes/navbar.php'; ?>


<main>

<section class="order-section">


    <!-- ================================
         HEADING
         ================================ -->

    <div class="order-heading">

        <p class="section-label">
            SHOP
        </p>

        <h1>
            Order your <em>tea.</em>
        </h1>

        <p>
            Choose a tea and quantity below to
            place your order.
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


    <!-- ================================
         ORDER FORM
         ================================ -->

    <div class="order-card">

        <p class="section-label">
            PLACE ORDER
        </p>

        <h2>
            Choose your <em>tea.</em>
        </h2>


        <form method="POST"
              action="order.php">


            <label for="product">
                Tea
            </label>

            <select
                id="product"
                name="product_id"
                required
            >

                <option value="">
                    Select a tea
                </option>

                <?php foreach ($products as $product): ?>

                    <option
                        value="<?= (int) $product['product_id'] ?>"
                        <?= $product['quantity'] <= 0
                            ? 'disabled'
                            : '' ?>
                    >

                        <?= htmlspecialchars($product['name']) ?>

                        —
                        ₱<?= number_format($product['price'], 2) ?>

                        (Stock:
                        <?= (int) $product['quantity'] ?>)

                    </option>

                <?php endforeach; ?>

            </select>


            <label for="quantity">
                Quantity
            </label>

            <input
                type="number"
                id="quantity"
                name="quantity"
                min="1"
                value="1"
                required
            >


            <button
                type="submit"
                name="place_order"
                class="primary-button">

                Place Order →

            </button>

        </form>

    </div>


    <!-- ================================
         CURRENT PRODUCTS
         ================================ -->

    <div class="order-products">

        <p class="section-label">
            AVAILABLE TEAS
        </p>

        <h2>
            Our <em>collection.</em>
        </h2>


        <div class="order-product-list">

            <?php foreach ($products as $product): ?>

                <div class="order-product">

                    <div>

                        <p class="tea-type">
                            <?= htmlspecialchars(
                                $product['tea_type']
                            ) ?>
                        </p>

                        <h3>
                            <?= htmlspecialchars(
                                $product['name']
                            ) ?>
                        </h3>

                    </div>

                    <div class="order-product-price">

                        <strong>
                            ₱<?= number_format(
                                $product['price'],
                                2
                            ) ?>
                        </strong>

                        <span>
                            Stock:
                            <?= (int) $product['quantity'] ?>
                        </span>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>


</section>

</main>

</body>

</html>