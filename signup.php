<?php
require_once 'includes/session.php';
require_once 'includes/database.php';
require_once 'includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/home.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($full_name === '' || $email === '' || $phone === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } elseif (!ctype_digit($phone)) {
        $error = 'Phone number should contain only digits.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT user_id FROM tbl_users WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = 'An account with this email already exists.';
        } else {
            // signup.php — hashing
            $hashed_password = md5($password);

            $insert = mysqli_prepare($conn, 'INSERT INTO tbl_users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, "user")');
            mysqli_stmt_bind_param($insert, 'ssss', $full_name, $email, $phone, $hashed_password);

            if (mysqli_stmt_execute($insert)) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}

$page_title = 'Sign Up';
require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-form-panel">
        <div class="auth-logo"><?php echo SITE_NAME; ?></div>
        <h2 class="auth-title">Create Account</h2>
        <p class="auth-subtitle">Sign up to start renting cameras</p>

        <?php if ($error !== ''): ?>
            <div class="form-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="signup.php">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" value="<?php echo isset($full_name) ? htmlspecialchars($full_name) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
            </div>

            <button type="submit" class="btn-primary">Sign Up</button>
        </form>

        <p class="auth-footer-text">Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>