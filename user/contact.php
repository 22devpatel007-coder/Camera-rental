<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$error = '';
$success = false;

$name = '';
$email = '';
$subject = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO tbl_contact (name, email, subject, message) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $subject, $message);

        if (mysqli_stmt_execute($stmt)) {
            $success = true;
            $name = '';
            $email = '';
            $subject = '';
            $message = '';
        } else {
            $error = 'Something went wrong. Please try again.';
        }
        mysqli_stmt_close($stmt);
    }
}

$page_title = 'Contact Us';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <h2 class="section-title">Contact Us</h2>

    <?php if ($success): ?>
        <div class="form-success">Thank you for reaching out. We'll get back to you soon.</div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="form-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="contact.php" class="admin-form">
        <div class="form-group">
            <label for="name">Your Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Your Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>

        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>" required>
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="6" required><?php echo htmlspecialchars($message); ?></textarea>
        </div>

        <button type="submit" class="btn-primary">Send Message</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>