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


/* ================================
   DATABASE COUNTS
   ================================ */

$product_count = $pdo->query("
    SELECT COUNT(*) FROM products
")->fetchColumn();

$inventory_count = $pdo->query("
    SELECT COUNT(*) FROM inventory
")->fetchColumn();

$user_count = $pdo->query("
    SELECT COUNT(*) FROM users
")->fetchColumn();

$message_count = $pdo->query("
    SELECT COUNT(*) FROM contact_messages
")->fetchColumn();
$order_count = $pdo->query("
    SELECT COUNT(*) FROM orders
")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<?php include '../includes/navbar.php'; ?>


<main>

    <section class="admin-dashboard-section">

        <div class="admin-dashboard-heading">

            <p class="section-label">
                ADMIN DASHBOARD
            </p>

            <h1>
                Welcome, <em><?= htmlspecialchars($_SESSION['admin_name']) ?>.</em>
            </h1>

            <p>
                Manage Leaf &amp; Bloom products, inventory,
                users, and customer messages.
            </p>

        </div>


        <div class="admin-stats">

            <div class="admin-stat-card">
                <span class="admin-stat-number">
                    <?= $order_count ?>
                </span>
                <span class="admin-stat-label">
                    CUSTOMER ORDERS
                </span>
            </div>


            <div class="admin-stat-card">

                <span class="admin-stat-number">
                    <?= $product_count ?>
                </span>

                <span class="admin-stat-label">
                    PRODUCTS
                </span>

            </div>


            <div class="admin-stat-card">

                <span class="admin-stat-number">
                    <?= $inventory_count ?>
                </span>

                <span class="admin-stat-label">
                    INVENTORY ITEMS
                </span>

            </div>


            <div class="admin-stat-card">

                <span class="admin-stat-number">
                    <?= $user_count ?>
                </span>

                <span class="admin-stat-label">
                    REGISTERED USERS
                </span>

            </div>


            <div class="admin-stat-card">

                <span class="admin-stat-number">
                    <?= $message_count ?>
                </span>

                <span class="admin-stat-label">
                    CONTACT MESSAGES
                </span>

            </div>

        </div>


        <div class="admin-actions">

        <div class="admin-action-card">

    <p class="section-label">
        ORDERS
    </p>

    <h2>
        Customer <em>orders.</em>
    </h2>

    <p>
        Review customer orders and update
        their current order status.
    </p>

    <a href="orders.php"
       class="primary-button">
        Manage Orders →
    </a>

</div>

            <div class="admin-action-card">

            

                <p class="section-label">
                    PRODUCTS
                </p>

                <h2>
                    Manage <em>teas.</em>
                </h2>

                <p>
                    Add, edit, and remove products
                    from the Leaf &amp; Bloom collection.
                </p>

                <a href="products.php"
                   class="primary-button">
                    Manage Products →
                </a>

            </div>


            <div class="admin-action-card">

                <p class="section-label">
                    INVENTORY
                </p>

                <h2>
                    Manage <em>stock.</em>
                </h2>

                <p>
                    View and update the available
                    quantity of each tea.
                </p>

                <a href="inventory.php"
   class="primary-button">
    Manage Inventory →
</a>
            </div>


            <div class="admin-action-card">

                <p class="section-label">
                    USERS
                </p>

                <h2>
                    View <em>users.</em>
                </h2>

                <p>
                    Check the registered users
                    stored in the database.
                </p>

                <a href="users.php"
                   class="primary-button">
                    View Users →
                </a>

            </div>


            <div class="admin-action-card">

                <p class="section-label">
                    MESSAGES
                </p>

                <h2>
                    Customer <em>messages.</em>
                </h2>

                <p>
                    View messages submitted through
                    the contact form.
                </p>

                <a href="messages.php"
                   class="primary-button">
                    View Messages →
                </a>

            </div>

        </div>


        <div class="admin-dashboard-footer">

            <a href="../index.php"
               class="secondary-button">
                ← Back to Website
            </a>

            <a href="logout.php"
               class="secondary-button">
                Log Out
            </a>

        </div>

    </section>

</main>

</body>

</html>