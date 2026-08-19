<?php

require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/config.php';
require_once '../includes/user-auth.php';

$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;

if ($booking_id <= 0) {
    header('Location: browse-cameras.php');
    exit;
}

// Fetch booking, restricted to the logged-in user
$stmt = mysqli_prepare($conn, 'SELECT booking_id, booking_number, pickup_date, return_date, rental_type, total_days, total_hours, total_amount, booking_status, payment_method, booking_date
     FROM tbl_bookings WHERE booking_id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'ii', $booking_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result(
    $stmt,
    $bk_booking_id, $bk_booking_number, $bk_pickup_date, $bk_return_date,
    $bk_rental_type, $bk_total_days, $bk_total_hours, $bk_total_amount,
    $bk_booking_status, $bk_payment_method, $bk_booking_date
);

$booking = false;
if (mysqli_stmt_fetch($stmt)) {
    $booking = array(
        'booking_id'     => $bk_booking_id,
        'booking_number' => $bk_booking_number,
        'pickup_date'    => $bk_pickup_date,
        'return_date'    => $bk_return_date,
        'rental_type'    => $bk_rental_type,
        'total_days'     => $bk_total_days,
        'total_hours'    => $bk_total_hours,
        'total_amount'   => $bk_total_amount,
        'booking_status' => $bk_booking_status,
        'payment_method' => $bk_payment_method,
        'booking_date'   => $bk_booking_date,
    );
}
mysqli_stmt_close($stmt);

if (!$booking) {
    header('Location: browse-cameras.php');
    exit;
}

// Fetch booking items with camera details
$items_stmt = mysqli_prepare($conn, 'SELECT bi.quantity, bi.price_per_day, bi.subtotal, c.camera_name, c.image
    FROM tbl_booking_items bi
    JOIN tbl_cameras c ON bi.camera_id = c.camera_id
    WHERE bi.booking_id = ?');
mysqli_stmt_bind_param($items_stmt, 'i', $booking_id);
mysqli_stmt_execute($items_stmt);
mysqli_stmt_bind_result($items_stmt, $it_quantity, $it_price_per_day, $it_subtotal, $it_camera_name, $it_image);

$items = array();
while (mysqli_stmt_fetch($items_stmt)) {
    $items[] = array(
        'quantity'      => $it_quantity,
        'price_per_day' => $it_price_per_day,
        'subtotal'      => $it_subtotal,
        'camera_name'   => $it_camera_name,
        'image'         => $it_image,
    );
}
mysqli_stmt_close($items_stmt);

$page_title = 'Booking Confirmation';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container confirmation-page">
    <div class="confirmation-header">
        <h2>Booking Confirmed</h2>
        <p class="confirmation-sub">Booking Number: <strong><?php echo htmlspecialchars($booking['booking_number']); ?></strong></p>
    </div>

    <div class="confirmation-card">
        <div class="confirmation-meta">
            <div>
                <span class="meta-label">Pickup Date</span>
                <span class="meta-value"><?php echo htmlspecialchars($booking['pickup_date']); ?></span>
            </div>
            <div>
                <span class="meta-label">Return Date</span>
                <span class="meta-value"><?php echo htmlspecialchars($booking['return_date']); ?></span>
            </div>
             <div>
                <span class="meta-label">Rental Type</span>
                <span class="meta-value"><?php echo htmlspecialchars($booking['rental_type']); ?></span>
            </div>
            <div>
                <span class="meta-label">Duration</span>
                <span class="meta-value">
                    <?php echo ($booking['rental_type'] === 'Hourly') ? (int) $booking['total_hours'] . ' hr(s)' : (int) $booking['total_days'] . ' day(s)'; ?>
                </span>
            </div>
            <div>
                <span class="meta-label">Status</span>
                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $booking['booking_status'])); ?>">
                    <?php echo htmlspecialchars($booking['booking_status']); ?>
                </span>
            </div>
            <div>
                <span class="meta-label">Payment Method</span>
                <span class="meta-value"><?php echo htmlspecialchars($booking['payment_method']); ?></span>
            </div>
        </div>

        <table class="confirmation-items">
            <thead>
                <tr>
                    <th>Camera</th>
                    <th>Price/Day</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['camera_name']); ?></td>
                    <td>₹<?php echo number_format($item['price_per_day'], 2); ?></td>
                    <td><?php echo (int) $item['quantity']; ?></td>
                    <td>₹<?php echo number_format($item['subtotal'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="confirmation-total">
            <span>Total Amount</span>
            <span>₹<?php echo number_format($booking['total_amount'], 2); ?></span>
        </div>
    </div>

    <a href="browse-cameras.php" class="btn-primary confirmation-btn">Continue Browsing</a>
</div>

<?php require_once '../includes/footer.php'; ?>