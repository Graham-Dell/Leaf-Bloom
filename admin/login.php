<?php

session_start();

require_once '../includes/db.php';

$message = "";
$message_type = "";


/* ================================
   ADMIN LOGIN
   ================================ */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === "" || $password === "") {

        $message = "Please enter your email and password.";
        $message_type = "error";

    } else {

        $stmt = $pdo->prepare("
            SELECT admin_id, name, email, password
            FROM admins
            WHERE email = ?
        ");

        $stmt->execute([$email]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {

            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];

            header("Location: dashboard.php");
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

    <title>Admin Login | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="../assets/css/style.css">

</head>

<body>

<?php include '../includes/navbar.php'; ?>


<main>

    <section class="account-section">

        <div class="account-heading">

            <p class="section-label">
                ADMIN
            </p>

            <h1>
                Admin <em>sign in.</em>
            </h1>

            <p>
                Sign in to manage Leaf &amp; Bloom.
            </p>

        </div>


        <?php if ($message !== ""): ?>

            <div class="account-message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>


        <div class="account-card admin-login-card">

            <p class="section-label">
                ADMIN ACCESS
            </p>

            <h2>
                Welcome <em>back.</em>
            </h2>

            <form method="POST"
                  action="login.php">

                <label for="admin-email">
                    Email
                </label>

                <input
                    type="email"
                    id="admin-email"
                    name="email"
                    required
                >

                <label for="admin-password">
                    Password
                </label>

                <input
                    type="password"
                    id="admin-password"
                    name="password"
                    required
                >

                <button
                    type="submit"
                    class="primary-button">
                    Sign In →
                </button>

            </form>

        </div>

    </section>

</main>

</body>

</html>