<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

// Total Cameras
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_cameras"));
$total_cameras = $row['total'];

// Available Cameras
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_cameras WHERE status = 'Available'"));
$available_cameras = $row['total'];

// Total Customers (role = user)
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_users WHERE role = 'user'"));
$total_customers = $row['total'];

// Total Bookings
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_bookings"));
$total_bookings = $row['total'];

// Active Rentals (Approved or Picked Up)
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_bookings WHERE booking_status IN ('Approved', 'Picked Up')"));
$active_rentals = $row['total'];

$page_title = 'Dashboard';
require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';
?>

<div class="admin-content">
    <h2>Dashboard</h2>

    <div class="stats-grid">
        <div class="stat-card">
            <p class="stat-label">Total Cameras</p>
            <p class="stat-value"><?php echo (int)$total_cameras; ?></p>
        </div>

        <div class="stat-card">
            <p class="stat-label">Available Cameras</p>
            <p class="stat-value"><?php echo (int)$available_cameras; ?></p>
        </div>

        <div class="stat-card">
            <p class="stat-label">Total Customers</p>
            <p class="stat-value"><?php echo (int)$total_customers; ?></p>
        </div>

        <div class="stat-card">
            <p class="stat-label">Total Bookings</p>
            <p class="stat-value"><?php echo (int)$total_bookings; ?></p>
        </div>

        <div class="stat-card">
            <p class="stat-label">Active Rentals</p>
            <p class="stat-value"><?php echo (int)$active_rentals; ?></p>
        </div>
    </div>
</div>
</div>

<?php require_once '../includes/footer.php'; ?>