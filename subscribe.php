<?php

session_start();

require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$email = trim($_POST['email'] ?? '');

if ($email === '') {
    $_SESSION['newsletter_error'] = "Please enter your email address.";
    header("Location: index.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['newsletter_error'] = "Please enter a valid email address.";
    header("Location: index.php");
    exit;
}

try {

    $stmt = $pdo->prepare("
        INSERT INTO newsletter_subscribers (email)
        VALUES (?)
    ");

    $stmt->execute([$email]);

    $_SESSION['newsletter_success'] =
        "You're subscribed! Welcome to Leaf & Bloom.";

} catch (PDOException $e) {

    if ($e->getCode() === '23000') {

        $_SESSION['newsletter_error'] =
            "This email is already subscribed.";

    } else {

        $_SESSION['newsletter_error'] =
            "Something went wrong. Please try again.";
    }
}

header("Location: index.php#newsletter");
exit;