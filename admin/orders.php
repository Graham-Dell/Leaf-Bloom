<?php

session_start();

require_once '../includes/db.php';


/* ================================
   ADMIN ACCESS CHECK
   ================================ */

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}


$message = "";
$message_type = "";


/* ================================
   UPDATE ORDER STATUS
   ================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['update_status'])) {

    $order_id = (int) $_POST['order_id'];
    $status = trim($_POST['status']);

    $allowed_statuses = [
        'Pending',
        'Confirmed',
        'Preparing',
        'Completed',
        'Cancelled'
    ];

    if (
        $order_id <= 0 ||
        !in_array($status, $allowed_statuses, true)
    ) {

        $message = "Invalid order status.";
        $message_type = "error";

    } else {

        $stmt = $pdo->prepare("
            UPDATE orders
            SET status = ?
            WHERE order_id = ?
        ");

        $stmt->execute([
            $status,
            $order_id
        ]);

        header("Location: orders.php?updated=1");
        exit;
    }
}


/* ================================
   SUCCESS MESSAGE
   ================================ */

if (isset($_GET['updated'])) {

    $message = "Order status updated successfully.";
    $message_type = "success";
}


/* ================================
   GET ORDERS
   ================================ */

$stmt = $pdo->query("
    SELECT
        orders.order_id,
        orders.total_amount,
        orders.status,
        orders.created_at,
        users.name AS customer_name,
        users.email AS customer_email

    FROM orders

    INNER JOIN users
        ON orders.user_id = users.user_id

    ORDER BY orders.created_at DESC
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ================================
   GET ORDER ITEMS
   ================================ */

$item_stmt = $pdo->query("
    SELECT
        order_items.order_id,
        products.name AS product_name,
        order_items.quantity,
        order_items.price

    FROM order_items

    INNER JOIN products
        ON order_items.product_id = products.product_id

    ORDER BY order_items.order_id DESC
");

$order_items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);


/* ================================
   ORGANIZE ITEMS BY ORDER
   ================================ */

$items_by_order = [];

foreach ($order_items as $item) {

    $items_by_order[$item['order_id']][] = $item;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Orders | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<?php include '../includes/navbar.php'; ?>


<main>

<section class="admin-orders-section">


    <!-- ================================
         HEADING
         ================================ -->

    <div class="admin-orders-heading">

        <p class="section-label">
            ADMIN · ORDERS
        </p>

        <h1>
            Manage <em>orders.</em>
        </h1>

        <p>
            Review customer orders and update
            their current status.
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
         ORDERS
         ================================ -->

    <div class="admin-orders-list">

        <?php if (empty($orders)): ?>

            <div class="admin-order-empty">

                <p>
                    No orders have been placed yet.
                </p>

            </div>

        <?php endif; ?>


        <?php foreach ($orders as $order): ?>

            <article class="admin-order-card">


                <div class="admin-order-header">

                    <div>

                        <p class="section-label">
                            ORDER #<?= (int) $order['order_id'] ?>
                        </p>

                        <h2>
                            <?= htmlspecialchars(
                                $order['customer_name']
                            ) ?>
                        </h2>

                        <p>
                            <?= htmlspecialchars(
                                $order['customer_email']
                            ) ?>
                        </p>

                    </div>


                    <div class="admin-order-total">

                        <strong>
                            ₱<?= number_format(
                                $order['total_amount'],
                                2
                            ) ?>
                        </strong>

                        <span>
                            <?= htmlspecialchars(
                                $order['created_at']
                            ) ?>
                        </span>

                    </div>

                </div>


                <!-- ORDER ITEMS -->

                <div class="admin-order-items">

                    <p class="section-label">
                        ORDER ITEMS
                    </p>

                    <?php
                    $items =
                        $items_by_order[$order['order_id']]
                        ?? [];
                    ?>

                    <?php foreach ($items as $item): ?>

                        <div class="admin-order-item">

                            <span>
                                <?= htmlspecialchars(
                                    $item['product_name']
                                ) ?>
                            </span>

                            <span>
                                <?= (int) $item['quantity'] ?>
                                ×
                                ₱<?= number_format(
                                    $item['price'],
                                    2
                                ) ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                </div>


                <!-- STATUS -->

                <div class="admin-order-status">

                    <form method="POST"
                          action="orders.php">

                        <input
                            type="hidden"
                            name="order_id"
                            value="<?= (int) $order['order_id'] ?>"
                        >

                        <label for="status-<?= (int) $order['order_id'] ?>">
                            Order Status
                        </label>

                        <select
                            id="status-<?= (int) $order['order_id'] ?>"
                            name="status"
                        >

                            <?php
                            $statuses = [
                                'Pending',
                                'Confirmed',
                                'Preparing',
                                'Completed',
                                'Cancelled'
                            ];
                            ?>

                            <?php foreach ($statuses as $status): ?>

                                <option
                                    value="<?= $status ?>"
                                    <?= $order['status'] === $status
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= $status ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <button
                            type="submit"
                            name="update_status"
                            class="primary-button"
                        >
                            Update Status →
                        </button>

                    </form>

                </div>


            </article>

        <?php endforeach; ?>

    </div>


    <div class="admin-dashboard-footer">

        <a href="dashboard.php"
           class="secondary-button">
            ← Back to Dashboard
        </a>

    </div>


</section>

</main>

</body>

</html>