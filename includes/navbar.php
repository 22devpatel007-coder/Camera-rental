<?php
// navbar.php — shared navigation bar for public/user pages
// Expects config.php and session.php to already be loaded by the parent page.
?>
<header class="site-navbar">
    <div class="container navbar-inner">
        <a href="<?php echo BASE_URL; ?>index.php" class="navbar-logo"><?php echo SITE_NAME; ?></a>

        <button type="button" class="navbar-toggle" id="navbarToggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="navbar-links" id="navbarLinks">
    <a href="<?php echo BASE_URL; ?>index.php">Home</a>
    <a href="<?php echo BASE_URL; ?>user/browse-cameras.php">Browse Cameras</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="<?php echo BASE_URL; ?>user/cart.php">Cart</a>
        <a href="<?php echo BASE_URL; ?>user/my-bookings.php">My Bookings</a>
        <!-- <a href="<?php echo BASE_URL; ?>user/home.php">My Account</a> -->
        <a href="<?php echo BASE_URL; ?>logout.php">Logout</a>
    <?php else: ?>
        <a href="<?php echo BASE_URL; ?>user/about.php">About</a>
        <a href="<?php echo BASE_URL; ?>user/contact.php">Contact</a>
        <a href="<?php echo BASE_URL; ?>login.php">Login</a>
        <a href="<?php echo BASE_URL; ?>signup.php" class="navbar-cta">Sign Up</a>
    <?php endif; ?>
</nav>
    </div>
</header>