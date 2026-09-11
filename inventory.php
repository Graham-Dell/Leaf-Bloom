<?php
require_once 'includes/db.php';

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inventory | Leaf & Bloom</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<?php include 'includes/navbar.php'; ?>

<main>

    <section class="inventory-section">

        <div class="inventory-heading">
            <p class="section-label">INVENTORY</p>

            <h1>
                Tea <em>inventory.</em>
            </h1>

            <p>
                View the current stock of our available teas.
            </p>
        </div>

        <div class="inventory-table-wrapper">

            <table class="inventory-table">

                <thead>
                    <tr>
                        <th>Tea</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($products as $product): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($product['name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($product['tea_type']) ?>
                            </td>

                            <td>
                                ₱<?= number_format($product['price'], 2) ?>
                            </td>

                            <td>
                                <?= (int) $product['quantity'] ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($product['updated_at']) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

</body>
</html>