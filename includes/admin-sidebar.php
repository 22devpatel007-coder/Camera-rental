<aside class="admin-sidebar">
    <div class="admin-sidebar-logo"><?php echo SITE_NAME; ?></div>

    <nav class="admin-nav">
        <a href="<?php echo BASE_URL; ?>admin/dashboard.php" class="<?php echo (basename($_SERVER['PHP_SELF']) === 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a>

        <a href="<?php echo BASE_URL; ?>admin/manage-cameras.php" class="<?php echo (strpos(basename($_SERVER['PHP_SELF']), 'camera') !== false) ? 'active' : ''; ?>">Cameras</a>

        <a href="<?php echo BASE_URL; ?>admin/manage-brands.php" class="<?php echo (strpos(basename($_SERVER['PHP_SELF']), 'brand') !== false) ? 'active' : ''; ?>">Brands</a>

        <a href="<?php echo BASE_URL; ?>admin/manage-customers.php" class="<?php echo (strpos(basename($_SERVER['PHP_SELF']), 'customer') !== false) ? 'active' : ''; ?>">Customers</a>

        <a href="<?php echo BASE_URL; ?>admin/manage-bookings.php" class="<?php echo (strpos(basename($_SERVER['PHP_SELF']), 'booking') !== false) ? 'active' : ''; ?>">Bookings</a>
        
        <a href="<?php echo BASE_URL; ?>admin/manage-contact.php" class="<?php echo (basename($_SERVER['PHP_SELF']) === 'manage-contact.php') ? 'active' : ''; ?>">Contact Messages</a>
     
        <a href="<?php echo BASE_URL; ?>admin/rent-status.php" class="<?php echo (basename($_SERVER['PHP_SELF']) === 'rent-status.php') ? 'active' : ''; ?>">Rent Status</a>


        <a href="<?php echo BASE_URL; ?>admin/logout.php" class="admin-logout">Logout</a>
    </nav>
</aside>