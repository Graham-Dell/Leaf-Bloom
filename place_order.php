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

$paymentMethod = $_POST['payment_method'] ?? 'Cash on Delivery';

try {

    /* ================================
       START DATABASE TRANSACTION
       ================================ */

    $pdo->beginTransaction();


    /* ================================
       GET CART PRODUCTS + LOCK STOCK
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
            inventory.quantity AS stock
        FROM products

        INNER JOIN inventory
            ON products.product_id = inventory.product_id

        WHERE products.product_id IN ($placeholders)

        FOR UPDATE
    ");

    $stmt->execute($product_ids);

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);


    /* ================================
       VERIFY PRODUCTS
       ================================ */

    if (count($products) !== count($product_ids)) {

        throw new Exception(
            "One or more products in your cart are no longer available."
        );
    }


    /* ================================
       CALCULATE TOTAL
       ================================ */

    $total = 0;

    foreach ($products as $product) {

        $product_id =
            (int) $product['product_id'];

        $requested_quantity =
            (int) $_SESSION['cart'][$product_id];

        $available_stock =
            (int) $product['stock'];


        if ($requested_quantity <= 0) {

            throw new Exception(
                "Invalid quantity for " .
                $product['name']
            );
        }


        if ($requested_quantity > $available_stock) {

            throw new Exception(
                "Not enough stock for " .
                $product['name']
            );
        }


        $total +=
            $product['price'] * $requested_quantity;
    }


    /* ================================
       CREATE ORDER
       ================================ */

   $stmt = $pdo->prepare("
    INSERT INTO orders (user_id, total_amount, payment_method)
    VALUES (?, ?, ?)
");

    $stmt->execute([
        $_SESSION['user_id'],
        $total,
        $paymentMethod
    ]);


    $order_id =
        $pdo->lastInsertId();


    /* ================================
       CREATE ORDER ITEMS
       ================================ */

    $item_stmt = $pdo->prepare("
        INSERT INTO order_items
        (order_id, product_id, quantity, price)
        VALUES (?, ?, ?, ?)
    ");


    /* ================================
       UPDATE INVENTORY
       ================================ */

    $inventory_stmt = $pdo->prepare("
        UPDATE inventory
        SET quantity = quantity - ?
        WHERE product_id = ?
    ");


    foreach ($products as $product) {

        $product_id =
            (int) $product['product_id'];

        $quantity =
            (int) $_SESSION['cart'][$product_id];


        /* Add item to order */

        $item_stmt->execute([
            $order_id,
            $product_id,
            $quantity,
            $product['price']
        ]);


        /* Decrease inventory */

        $inventory_stmt->execute([
            $quantity,
            $product_id
        ]);
    }


    /* ================================
       COMPLETE TRANSACTION
       ================================ */

    $pdo->commit();


    /* ================================
       CLEAR CART
       ================================ */

    $_SESSION['cart'] = [];


    /* ================================
       REDIRECT TO SUCCESS PAGE
       ================================ */

    header(
        "Location: order_success.php?order_id=" .
        $order_id
    );

    exit;


} catch (Exception $e) {


    /* ================================
       ROLLBACK IF SOMETHING FAILED
       ================================ */

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    $_SESSION['order_error'] =
        $e->getMessage();


    header("Location: checkout.php");

    exit;
}