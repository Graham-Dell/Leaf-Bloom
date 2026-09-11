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
   GET CONTACT MESSAGES
   ================================ */

$stmt = $pdo->query("
    SELECT
        message_id,
        name,
        email,
        subject,
        message,
        created_at
    FROM contact_messages
    ORDER BY created_at DESC
");

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Messages | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<?php include '../includes/navbar.php'; ?>


<main>

<section class="admin-messages-section">


    <!-- ================================
         HEADING
         ================================ -->

    <div class="admin-messages-heading">

        <p class="section-label">
            ADMIN · MESSAGES
        </p>

        <h1>
            Customer <em>messages.</em>
        </h1>

        <p>
            Review messages submitted through
            the Leaf &amp; Bloom contact form.
        </p>

    </div>


    <!-- ================================
         MESSAGE COUNT
         ================================ -->

    <div class="admin-messages-summary">

        <span>
            <?= count($messages) ?>
        </span>

        <p>
            CUSTOMER MESSAGES
        </p>

    </div>


    <!-- ================================
         MESSAGE LIST
         ================================ -->

    <div class="admin-message-list">

        <?php if (empty($messages)): ?>

            <div class="admin-message-empty">

                <p>
                    No customer messages found.
                </p>

            </div>

        <?php else: ?>

            <?php foreach ($messages as $message): ?>

                <article class="admin-message-card">


                    <div class="admin-message-header">

                        <div>

                            <p class="section-label">
                                MESSAGE #<?= (int) $message['message_id'] ?>
                            </p>

                            <h2>
                                <?= htmlspecialchars(
                                    $message['subject']
                                ) ?>
                            </h2>

                        </div>

                        <span class="admin-message-date">
                            <?= htmlspecialchars(
                                $message['created_at']
                            ) ?>
                        </span>

                    </div>


                    <div class="admin-message-details">

                        <div>

                            <span>
                                FROM
                            </span>

                            <strong>
                                <?= htmlspecialchars(
                                    $message['name']
                                ) ?>
                            </strong>

                        </div>


                        <div>

                            <span>
                                EMAIL
                            </span>

                            <strong>
                                <?= htmlspecialchars(
                                    $message['email']
                                ) ?>
                            </strong>

                        </div>

                    </div>


                    <div class="admin-message-body">

                        <span>
                            MESSAGE
                        </span>

                        <p>
                            <?= nl2br(
                                htmlspecialchars(
                                    $message['message']
                                )
                            ) ?>
                        </p>

                    </div>


                </article>

            <?php endforeach; ?>

        <?php endif; ?>

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