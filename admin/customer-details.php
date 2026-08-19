<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$user_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($user_id <= 0) {
    header('Location: manage-customers.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT user_id, full_name, email, phone, created_at FROM tbl_users WHERE user_id = ? AND role = 'user'");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $c_user_id, $c_full_name, $c_email, $c_phone, $c_created_at);
$customer_found = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$customer_found) {
    header('Location: manage-customers.php');
    exit;
}

$customer = array(
    'user_id'    => $c_user_id,
    'full_name'  => $c_full_name,
    'email'      => $c_email,
    'phone'      => $c_phone,
    'created_at' => $c_created_at
);

$bookings_stmt = mysqli_prepare($conn, "SELECT booking_id, booking_number, pickup_date, return_date, rental_type, total_amount, booking_status, booking_date
                                         FROM tbl_bookings WHERE user_id = ? ORDER BY booking_date DESC");
mysqli_stmt_bind_param($bookings_stmt, 'i', $user_id);
mysqli_stmt_execute($bookings_stmt);
mysqli_stmt_bind_result($bookings_stmt, $b_booking_id, $b_booking_number, $b_pickup_date, $b_return_date, $b_rental_type, $b_total_amount, $b_booking_status, $b_booking_date);

$bookings = array();
while (mysqli_stmt_fetch($bookings_stmt)) {
    $bookings[] = array(
        'booking_id'     => $b_booking_id,
        'booking_number' => $b_booking_number,
        'pickup_date'    => $b_pickup_date,
        'return_date'    => $b_return_date,
        'rental_type'    => $b_rental_type,
        'total_amount'   => $b_total_amount,
        'booking_status' => $b_booking_status,
        'booking_date'   => $b_booking_date
    );
}
mysqli_stmt_close($bookings_stmt);

$page_title = 'Customer Details';
require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h2>Customer Details</h2>
        <a href="manage-customers.php" class="btn-secondary">Back to Customers</a>
    </div>

    <div class="details-card">
        <div class="details-row">
            <span class="meta-label">Full Name</span>
            <span class="meta-value"><?php echo htmlspecialchars($customer['full_name']); ?></span>
        </div>
        <div class="details-row">
            <span class="meta-label">Email</span>
            <span class="meta-value"><?php echo htmlspecialchars($customer['email']); ?></span>
        </div>
        <div class="details-row">
            <span class="meta-label">Phone</span>
            <span class="meta-value"><?php echo htmlspecialchars($customer['phone']); ?></span>
        </div>
        <div class="details-row">
            <span class="meta-label">Joined</span>
            <span class="meta-value"><?php echo htmlspecialchars($customer['created_at']); ?></span>
        </div>
    </div>

    <h3>Booking History</h3>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Booking No.</th>
                <th>Pickup</th>
                <th>Return</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($bookings) === 0): ?>
                <tr><td colspan="6">No bookings yet.</td></tr>
            <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($b['booking_number']); ?></td>
                        <td><?php echo htmlspecialchars($b['pickup_date']); ?></td>
                        <td><?php echo htmlspecialchars($b['return_date']); ?></td>
                        <td><?php echo htmlspecialchars($b['rental_type']); ?></td>
                        <td>&#8377;<?php echo number_format($b['total_amount'], 2); ?></td>
                        <td><span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $b['booking_status'])); ?>"><?php echo htmlspecialchars($b['booking_status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>