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
   DELETE PRODUCT
   ================================ */

if (isset($_GET['delete'])) {

    $product_id = (int) $_GET['delete'];

    try {

        $pdo->beginTransaction();

        /*
         * Delete inventory first because
         * inventory.product_id references products.product_id.
         */
        $stmt = $pdo->prepare("
            DELETE FROM inventory
            WHERE product_id = ?
        ");

        $stmt->execute([$product_id]);


        /*
         * Delete the product itself.
         */
        $stmt = $pdo->prepare("
            DELETE FROM products
            WHERE product_id = ?
        ");

        $stmt->execute([$product_id]);

        $pdo->commit();

        header("Location: products.php?deleted=1");
        exit;

    } catch (PDOException $e) {

        $pdo->rollBack();

        $message = "Unable to delete the product.";
        $message_type = "error";
    }
}


/* ================================
   SUCCESS MESSAGE
   ================================ */

if (isset($_GET['deleted'])) {

    $message = "Product deleted successfully.";
    $message_type = "success";
}


/* ================================
   ADD PRODUCT
   ================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['add_product'])) {

    $name = trim($_POST['name']);
    $tea_type = trim($_POST['tea_type']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);
    $category_id = (int) $_POST['category_id'];
    $quantity = (int) $_POST['quantity'];


    if (
        $name === "" ||
        $tea_type === "" ||
        $price === "" ||
        $description === "" ||
        $image === "" ||
        $category_id <= 0 ||
        $quantity < 0
    ) {

        $message = "Please fill in all product fields correctly.";
        $message_type = "error";

    } elseif (!is_numeric($price) || $price < 0) {

        $message = "Please enter a valid price.";
        $message_type = "error";

    } else {

        try {

            $pdo->beginTransaction();


            /*
             * Insert product.
             */
            $stmt = $pdo->prepare("
                INSERT INTO products
                (category_id, name, tea_type, price, description, image)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $category_id,
                $name,
                $tea_type,
                $price,
                $description,
                $image
            ]);


            /*
             * Get the newly created product ID.
             */
            $product_id = $pdo->lastInsertId();


            /*
             * Create its inventory record.
             */
            $stmt = $pdo->prepare("
                INSERT INTO inventory
                (product_id, quantity)
                VALUES (?, ?)
            ");

            $stmt->execute([
                $product_id,
                $quantity
            ]);


            $pdo->commit();

            header("Location: products.php?added=1");
            exit;

        } catch (PDOException $e) {

            $pdo->rollBack();

            $message = "Unable to add the product.";
            $message_type = "error";
        }
    }
}


if (isset($_GET['added'])) {

    $message = "Product added successfully.";
    $message_type = "success";
}


/* ================================
   UPDATE PRODUCT
   ================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST['update_product'])) {

    $product_id = (int) $_POST['product_id'];

    $name = trim($_POST['name']);
    $tea_type = trim($_POST['tea_type']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);
    $category_id = (int) $_POST['category_id'];
    $quantity = (int) $_POST['quantity'];


    if (
        $product_id <= 0 ||
        $name === "" ||
        $tea_type === "" ||
        $price === "" ||
        $description === "" ||
        $image === "" ||
        $category_id <= 0 ||
        $quantity < 0
    ) {

        $message = "Please fill in all product fields correctly.";
        $message_type = "error";

    } elseif (!is_numeric($price) || $price < 0) {

        $message = "Please enter a valid price.";
        $message_type = "error";

    } else {

        try {

            $pdo->beginTransaction();


            /*
             * Update product information.
             */
            $stmt = $pdo->prepare("
                UPDATE products
                SET
                    category_id = ?,
                    name = ?,
                    tea_type = ?,
                    price = ?,
                    description = ?,
                    image = ?
                WHERE product_id = ?
            ");

            $stmt->execute([
                $category_id,
                $name,
                $tea_type,
                $price,
                $description,
                $image,
                $product_id
            ]);


            /*
             * Update inventory.
             */
            $stmt = $pdo->prepare("
                UPDATE inventory
                SET quantity = ?
                WHERE product_id = ?
            ");

            $stmt->execute([
                $quantity,
                $product_id
            ]);


            $pdo->commit();

            header("Location: products.php?updated=1");
            exit;

        } catch (PDOException $e) {

            $pdo->rollBack();

            $message = "Unable to update the product.";
            $message_type = "error";
        }
    }
}


if (isset($_GET['updated'])) {

    $message = "Product updated successfully.";
    $message_type = "success";
}


/* ================================
   GET CATEGORIES
   ================================ */

$category_stmt = $pdo->query("
    SELECT category_id, name
    FROM categories
    ORDER BY category_id
");

$categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);


/* ================================
   GET PRODUCTS
   ================================ */

$product_stmt = $pdo->query("
    SELECT
        products.product_id,
        products.name,
        products.tea_type,
        products.price,
        products.description,
        products.image,
        products.category_id,
        categories.name AS category_name,
        inventory.quantity
    FROM products

    INNER JOIN categories
        ON products.category_id = categories.category_id

    LEFT JOIN inventory
        ON products.product_id = inventory.product_id

    ORDER BY products.product_id
");

$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);


/* ================================
   EDIT MODE
   ================================ */

$edit_product = null;

if (isset($_GET['edit'])) {

    $edit_id = (int) $_GET['edit'];

    $stmt = $pdo->prepare("
        SELECT
            products.product_id,
            products.name,
            products.tea_type,
            products.price,
            products.description,
            products.image,
            products.category_id,
            inventory.quantity
        FROM products

        LEFT JOIN inventory
            ON products.product_id = inventory.product_id

        WHERE products.product_id = ?
    ");

    $stmt->execute([$edit_id]);

    $edit_product = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Products | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<?php include '../includes/navbar.php'; ?>


<main>

<section class="admin-products-section">


    <!-- ================================
         PAGE HEADING
         ================================ -->

    <div class="admin-products-heading">

        <p class="section-label">
            ADMIN · PRODUCTS
        </p>

        <h1>
            Manage <em>teas.</em>
        </h1>

        <p>
            Add, edit, and remove products from
            the Leaf &amp; Bloom collection.
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
         ADD / EDIT FORM
         ================================ -->

    <div class="admin-product-form-card">

        <p class="section-label">

            <?= $edit_product
                ? "EDIT PRODUCT"
                : "ADD PRODUCT" ?>

        </p>

        <h2>

            <?= $edit_product
                ? "Update your <em>tea.</em>"
                : "Add a new <em>tea.</em>" ?>

        </h2>


        <form method="POST"
              action="products.php<?= $edit_product ? '?edit=' . $edit_product['product_id'] : '' ?>">


            <?php if ($edit_product): ?>

                <input
                    type="hidden"
                    name="product_id"
                    value="<?= (int) $edit_product['product_id'] ?>"
                >

            <?php endif; ?>


            <label for="product-name">
                Tea Name
            </label>

            <input
                type="text"
                id="product-name"
                name="name"
                value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>"
                required
            >


            <label for="tea-type">
                Tea Type
            </label>

            <input
                type="text"
                id="tea-type"
                name="tea_type"
                placeholder="Example: GREEN TEA"
                value="<?= htmlspecialchars($edit_product['tea_type'] ?? '') ?>"
                required
            >


            <label for="category">
                Category
            </label>

            <select
                id="category"
                name="category_id"
                required
            >

                <option value="">
                    Select a category
                </option>

                <?php foreach ($categories as $category): ?>

                    <option
                        value="<?= (int) $category['category_id'] ?>"
                        <?= (
                            isset($edit_product['category_id'])
                            && $edit_product['category_id'] == $category['category_id']
                        )
                            ? 'selected'
                            : '' ?>
                    >

                        <?= htmlspecialchars($category['name']) ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <label for="price">
                Price
            </label>

            <input
                type="number"
                id="price"
                name="price"
                step="0.01"
                min="0"
                value="<?= htmlspecialchars($edit_product['price'] ?? '') ?>"
                required
            >


            <label for="quantity">
                Stock Quantity
            </label>

            <input
                type="number"
                id="quantity"
                name="quantity"
                min="0"
                value="<?= htmlspecialchars($edit_product['quantity'] ?? '0') ?>"
                required
            >


            <label for="image">
                Image Filename
            </label>

            <input
                type="text"
                id="image"
                name="image"
                placeholder="example.jpg"
                value="<?= htmlspecialchars($edit_product['image'] ?? '') ?>"
                required
            >


            <label for="description">
                Description
            </label>

            <textarea
                id="description"
                name="description"
                rows="5"
                required
            ><?= htmlspecialchars($edit_product['description'] ?? '') ?></textarea>


            <div class="admin-form-buttons">

                <button
                    type="submit"
                    name="<?= $edit_product ? 'update_product' : 'add_product' ?>"
                    class="primary-button"
                >

                    <?= $edit_product
                        ? "Update Product →"
                        : "Add Product →" ?>

                </button>


                <?php if ($edit_product): ?>

                    <a href="products.php"
                       class="secondary-button">
                        Cancel
                    </a>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <!-- ================================
         PRODUCT LIST
         ================================ -->

    <div class="admin-product-list">

        <div class="admin-product-list-heading">

            <div>

                <p class="section-label">
                    PRODUCT DATABASE
                </p>

                <h2>
                    Current <em>teas.</em>
                </h2>

            </div>

            <span>
                <?= count($products) ?> products
            </span>

        </div>


        <div class="admin-product-table-wrapper">

            <table class="admin-product-table">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Tea</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>

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
                                    <?= htmlspecialchars($product['name']) ?>
                                </strong>

                                <small>
                                    <?= htmlspecialchars($product['tea_type']) ?>
                                </small>

                            </td>

                            <td>
                                <?= htmlspecialchars($product['category_name']) ?>
                            </td>

                            <td>
                                ₱<?= number_format($product['price'], 2) ?>
                            </td>

                            <td>
                                <?= (int) $product['quantity'] ?>
                            </td>

                            <td>

                                <div class="admin-product-actions">

                                    <a
                                        href="products.php?edit=<?= (int) $product['product_id'] ?>"
                                        class="admin-edit-button"
                                    >
                                        Edit
                                    </a>

                                    <a
                                        href="products.php?delete=<?= (int) $product['product_id'] ?>"
                                        class="admin-delete-button"
                                        onclick="return confirm('Are you sure you want to delete this product?');"
                                    >
                                        Delete
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

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