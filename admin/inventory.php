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
   UPDATE INVENTORY
   ================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['update_inventory'])) {

    $product_id = (int) $_POST['product_id'];
    $quantity = (int) $_POST['quantity'];

    if ($product_id <= 0 || $quantity < 0) {

        $message = "Please enter a valid stock quantity.";
        $message_type = "error";

    } else {

        $stmt = $pdo->prepare("
            UPDATE inventory
            SET quantity = ?
            WHERE product_id = ?
        ");

        $stmt->execute([
            $quantity,
            $product_id
        ]);

        header("Location: inventory.php?updated=1");
        exit;
    }
}


/* ================================
   SUCCESS MESSAGE
   ================================ */

if (isset($_GET['updated'])) {

    $message = "Inventory updated successfully.";
    $message_type = "success";
}


/* ================================
   GET INVENTORY
   ================================ */

$stmt = $pdo->query("
    SELECT
        products.product_id,
        products.name,
        products.tea_type,
        products.price,
        inventory.quantity,
        inventory.updated_at
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

    <title>Manage Inventory | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<?php include '../includes/navbar.php'; ?>


<main>

<section class="admin-inventory-section">


    <div class="admin-inventory-heading">

        <p class="section-label">
            ADMIN · INVENTORY
        </p>

        <h1>
            Manage <em>stock.</em>
        </h1>

        <p>
            View and update the available quantity
            of each Leaf &amp; Bloom tea.
        </p>

    </div>


    <?php if ($message !== ""): ?>

        <div class="account-message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <div class="admin-inventory-table-wrapper">

        <table class="admin-inventory-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Tea</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Current Stock</th>
                    <th>Update Stock</th>
                </tr>

            </thead>

            <tbody>

                <?php foreach ($products as $product): ?>

                    <tr>

                        <td>
                            <?= (int) $product['product_id'] ?>
                        </td>

                        <td>
                            <strong>
                                <?= htmlspecialchars(
                                    $product['name']
                                ) ?>
                            </strong>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $product['tea_type']
                            ) ?>
                        </td>

                        <td>
                            ₱<?= number_format(
                                $product['price'],
                                2
                            ) ?>
                        </td>

                        <td>

                            <span class="inventory-stock-number">
                                <?= (int) $product['quantity'] ?>
                            </span>

                        </td>

                        <td>

                            <form method="POST"
                                  action="inventory.php"
                                  class="admin-inventory-form">

                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?= (int) $product['product_id'] ?>"
                                >

                                <input
                                    type="number"
                                    name="quantity"
                                    min="0"
                                    value="<?= (int) $product['quantity'] ?>"
                                    required
                                >

                                <button
                                    type="submit"
                                    name="update_inventory"
                                    class="primary-button">
                                    Update
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

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