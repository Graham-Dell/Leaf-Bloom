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
   GET USERS
   ================================ */

$stmt = $pdo->query("
    SELECT
        user_id,
        name,
        email,
        created_at
    FROM users
    ORDER BY created_at DESC
");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Users | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<?php include '../includes/navbar.php'; ?>


<main>

<section class="admin-users-section">


    <!-- ================================
         HEADING
         ================================ -->

    <div class="admin-users-heading">

        <p class="section-label">
            ADMIN · USERS
        </p>

        <h1>
            Registered <em>users.</em>
        </h1>

        <p>
            View the customers who have created
            accounts with Leaf &amp; Bloom.
        </p>

    </div>


    <!-- ================================
         USER COUNT
         ================================ -->

    <div class="admin-users-summary">

        <span>
            <?= count($users) ?>
        </span>

        <p>
            REGISTERED USERS
        </p>

    </div>


    <!-- ================================
         USER TABLE
         ================================ -->

    <div class="admin-users-table-wrapper">

        <table class="admin-users-table">

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($users)): ?>

                    <tr>

                        <td colspan="4">
                            No registered users found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($users as $user): ?>

                        <tr>

                            <td>
                                <?= (int) $user['user_id'] ?>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars(
                                        $user['name']
                                    ) ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $user['email']
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $user['created_at']
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>


    <!-- ================================
         BACK BUTTON
         ================================ -->

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