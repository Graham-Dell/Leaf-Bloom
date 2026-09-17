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
   ORDER ID
   ================================ */

$order_id =
    isset($_GET['order_id'])
    ? (int) $_GET['order_id']
    : 0;


if ($order_id <= 0) {
    header("Location: index.php");
    exit;
}


/* ================================
   GET ORDER
   ================================ */

$stmt = $pdo->prepare("
    SELECT
        orders.order_id,
        orders.total_amount,
        orders.status,
        orders.payment_method,
        orders.created_at
    FROM orders
    WHERE orders.order_id = ?
      AND orders.user_id = ?
");

$stmt->execute([
    $order_id,
    $_SESSION['user_id']
]);

$order = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$order) {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Order Confirmed | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<?php include 'includes/navbar.php'; ?>


<main>

<section class="order-success-section">

    <div class="order-success-card receipt">

        <p class="section-label">
            ORDER CONFIRMED
        </p>

        <h1>
            Thank you, <em><?= htmlspecialchars(
                $_SESSION['user_name']
            ) ?>.</em>
        </h1>

        <p class="order-success-text">
            Your tea order has been successfully placed.
            We'll prepare it with care.
        </p>


        <div class="order-success-details">

            <div>

                <span>
                    ORDER NUMBER
                </span>

                <strong>
                    #<?= (int) $order['order_id'] ?>
                </strong>

            </div>


            <div>

                <span>
                    STATUS
                </span>

                <strong>
                    <?= htmlspecialchars(
                        $order['status']
                    ) ?>
                </strong>

            </div>
            
<div>

    <span>
        PAYMENT METHOD
    </span>

    <strong>
        <?= htmlspecialchars($order['payment_method']) ?>
    </strong>

</div>

            <div>

                <span>
                    TOTAL
                </span>

                <strong>
                    ₱<?= number_format(
                        $order['total_amount'],
                        2
                    ) ?>
                </strong>

            </div>

        </div>

        <button type="button" onclick="window.print()" class="print-receipt">
    Print Receipt
</button>

        <div class="order-success-buttons">

            <a href="menu.php"
               class="primary-button">
                Continue Shopping →
            </a>

            <a href="index.php"
               class="secondary-button">
                Back to Home
            </a>

        </div>

    </div>

</section>

</main>

</body>

</html>