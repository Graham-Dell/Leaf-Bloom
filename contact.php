<?php
require_once 'includes/db.php';

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $user_message = trim($_POST['message']);

    if (
        $name === "" ||
        $email === "" ||
        $subject === "" ||
        $user_message === ""
    ) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    } else {

        $stmt = $pdo->prepare("
            INSERT INTO contact_messages
            (name, email, subject, message)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $email,
            $subject,
            $user_message
        ]);

        $message = "Thank you! Your message has been sent.";
        $message_type = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Contact | Leaf & Bloom</title>

    <link rel="stylesheet"
          href="assets/css/style.css">

</head>

<body>

<?php include 'includes/navbar.php'; ?>

<main>

    <section class="contact-section">

        <div class="contact-heading">

            <p class="section-label">
                CONTACT
            </p>

            <h1>
                Let's talk <em>tea.</em>
            </h1>

            <p>
                Have a question about our teas, your order,
                or simply want to say hello? We'd love to hear
                from you.
            </p>

        </div>


        <?php if ($message !== ""): ?>

            <div class="contact-message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>


        <div class="contact-grid">

            <!-- CONTACT INFORMATION -->

            <div class="contact-info">

                <p class="section-label">
                    GET IN TOUCH
                </p>

                <h2>
                    We'd love to<br>
                    hear from <em>you.</em>
                </h2>

                <p>
                    Whether you're looking for a particular tea,
                    need help with an order, or just want to
                    share your favourite brew, send us a message.
                </p>

                <div class="contact-details">

                    <div>
                        <span>Email</span>
                        <p>hello@leafandbloom.com</p>
                    </div>

                    <div>
                        <span>Hours</span>
                        <p>Monday – Saturday<br>9:00 AM – 6:00 PM</p>
                    </div>

                </div>

            </div>


            <!-- CONTACT FORM -->

            <div class="contact-card">

                <form method="POST"
                      action="contact.php">

                    <label for="name">
                        Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        required
                    >


                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                    >


                    <label for="subject">
                        Subject
                    </label>

                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        required
                    >


                    <label for="message">
                        Message
                    </label>

                    <textarea
                        id="message"
                        name="message"
                        rows="6"
                        required
                    ></textarea>


                    <button
                        type="submit"
                        class="primary-button">
                        Send Message →
                    </button>

                </form>

            </div>

        </div>

    </section>

</main>

</body>

</html>