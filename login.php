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
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = mysqli_prepare($conn, 'SELECT user_id, full_name, password, role FROM tbl_users WHERE email = ?');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        // login.php — verifying
        if ($user && md5($password) === $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'user/home.php'));
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

$page_title = 'Login';
require_once 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-form-panel">
        <div class="auth-logo"><?php echo SITE_NAME; ?></div>
        <h2 class="auth-title">Login</h2>
        <p class="auth-subtitle">Enter your details to continue</p>

        <?php if ($error !== ''): ?>
            <div class="form-error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif (isset($_GET['registered'])): ?>
            <div class="form-success">Account created successfully. Please login.</div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-primary">Login</button>
        </form>

        <p class="auth-footer-text">Don't have an account? <a href="signup.php">Sign Up</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>