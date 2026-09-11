<?php
session_start();

require_once 'includes/db.php';

$message = "";
$message_type = "";

/* ================================
   LOGOUT
   ================================ */

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();

    header("Location: account.php");
    exit;
}


/* ================================
   REGISTER
   ================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name === "" || $email === "" || $password === "") {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } elseif (strlen($password) < 6) {

        $message = "Password must be at least 6 characters.";
        $message_type = "error";

    } else {

        /* Check if email already exists */

        $stmt = $pdo->prepare(
            "SELECT user_id FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        if ($stmt->fetch()) {

            $message = "An account with that email already exists.";
            $message_type = "error";

        } else {

            /* Securely hash the password */

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $name,
                $email,
                $hashed_password
            ]);

            $message = "Account created successfully! You can now log in.";
            $message_type = "success";
        }
    }
}


/* ================================
   LOGIN
   ================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === "" || $password === "") {

        $message = "Please enter your email and password.";
        $message_type = "error";

    } else {

        $stmt = $pdo->prepare("
            SELECT user_id, name, email, password
            FROM users
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            header("Location: account.php");
            exit;

        } else {

            $message = "Incorrect email or password.";
            $message_type = "error";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Account | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<?php include 'includes/navbar.php'; ?>


<main>

    <section class="account-section">

        <div class="account-heading">

            <p class="section-label">
                ACCOUNT
            </p>

            <h1>
                Your tea <em>journey.</em>
            </h1>

            <p>
                Create an account or sign in to continue.
            </p>

        </div>


        <?php if ($message !== ""): ?>

            <div class="account-message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>


        <?php if (isset($_SESSION['user_id'])): ?>

    <div class="account-card">

        <p class="section-label">
            WELCOME BACK
        </p>

        <h2>
            Hello, <?= htmlspecialchars($_SESSION['user_name']) ?>.
        </h2>

        <p>
            You are currently signed in as
            <strong>
                <?= htmlspecialchars($_SESSION['user_email']) ?>
            </strong>.
        </p>

        <a href="account.php?logout=1"
           class="primary-button">
            Log Out
        </a>

    </div>


    <!-- ================================
         ORDER HISTORY
         ================================ -->

    <?php

    $stmt = $pdo->prepare("
        SELECT
            order_id,
            total_amount,
            status,
            created_at
        FROM orders
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");

    $stmt->execute([
        $_SESSION['user_id']
    ]);

    $user_orders =
        $stmt->fetchAll(PDO::FETCH_ASSOC);

    ?>


    <div class="account-orders">

        <div class="account-orders-heading">

            <p class="section-label">
                ORDER HISTORY
            </p>

            <h2>
                Your tea <em>orders.</em>
            </h2>

        </div>


        <?php if (empty($user_orders)): ?>

            <div class="account-order-empty">

                <p>
                    You haven't placed any orders yet.
                </p>

                <a href="menu.php"
                   class="secondary-button">
                    Explore Teas →
                </a>

            </div>


        <?php else: ?>


            <div class="account-order-list">

                <?php foreach ($user_orders as $order): ?>

                    <article class="account-order">

                        <div>

                            <span>
                                ORDER #<?= (int) $order['order_id'] ?>
                            </span>

                            <strong>
                                <?= htmlspecialchars(
                                    $order['status']
                                ) ?>
                            </strong>

                        </div>


                        <div>

                            <span>
                                DATE
                            </span>

                            <p>
                                <?= htmlspecialchars(
                                    $order['created_at']
                                ) ?>
                            </p>

                        </div>


                        <div>

                            <span>
                                TOTAL
                            </span>

                            <p>
                                ₱<?= number_format(
                                    $order['total_amount'],
                                    2
                                ) ?>
                            </p>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>


        <?php endif; ?>

    </div>




        <?php else: ?>

            <!-- ================================
                 REGISTER
                 ================================ -->

            <div class="account-grid">


                <div class="account-card">

                    <p class="section-label">
                        NEW HERE?
                    </p>

                    <h2>
                        Create an <em>account.</em>
                    </h2>

                    <form method="POST"
                          action="account.php">

                        <label for="register-name">
                            Name
                        </label>

                        <input
                            type="text"
                            id="register-name"
                            name="name"
                            required
                        >


                        <label for="register-email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="register-email"
                            name="email"
                            required
                        >


                        <label for="register-password">
                            Password
                        </label>

                        <input
                            type="password"
                            id="register-password"
                            name="password"
                            minlength="6"
                            required
                        >


                        <button
                            type="submit"
                            name="register"
                            class="primary-button">
                            Create Account →
                        </button>

                    </form>

                </div>


                <!-- ================================
                     LOGIN
                     ================================ -->

                <div class="account-card">

                    <p class="section-label">
                        WELCOME BACK
                    </p>

                    <h2>
                        Sign <em>in.</em>
                    </h2>

                    <form method="POST"
                          action="account.php">

                        <label for="login-email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="login-email"
                            name="email"
                            required
                        >


                        <label for="login-password">
                            Password
                        </label>

                        <input
                            type="password"
                            id="login-password"
                            name="password"
                            required
                        >


                        <button
                            type="submit"
                            name="login"
                            class="primary-button">
                            Sign In →
                        </button>

                    </form>

                </div>


            </div>

        <?php endif; ?>

    </section>

</main>

</body>

</html>