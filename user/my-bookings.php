<?php
require_once '../includes/user-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, 'SELECT booking_id, booking_number, pickup_date, return_date, rental_type, total_days, total_hours, total_amount, booking_status, payment_method, booking_date
     FROM tbl_bookings WHERE user_id = ? ORDER BY booking_date DESC');
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result(
    $stmt,
    $b_booking_id, $b_booking_number, $b_pickup_date, $b_return_date,
    $b_rental_type, $b_total_days, $b_total_hours, $b_total_amount,
    $b_booking_status, $b_payment_method, $b_booking_date
);

$bookings = array();
while (mysqli_stmt_fetch($stmt)) {
    $bookings[] = array(
        'booking_id'     => $b_booking_id,
        'booking_number' => $b_booking_number,
        'pickup_date'    => $b_pickup_date,
        'return_date'    => $b_return_date,
        'rental_type'    => $b_rental_type,
        'total_days'     => $b_total_days,
        'total_hours'    => $b_total_hours,
        'total_amount'   => $b_total_amount,
        'booking_status' => $b_booking_status,
        'payment_method' => $b_payment_method,
        'booking_date'   => $b_booking_date,
    );
}
mysqli_stmt_close($stmt);

$page_title = 'My Bookings';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>
<style>
    /* ===== My Bookings ===== */
.bookings-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.booking-card-link {
    display: block;
}

.booking-card {
    background-color: #0D0D0D;
    border: 1px solid #242424;
    border-radius: 12px;
    padding: 20px 25px;
    transition: border-color 0.2s;
}

.booking-card-link:hover .booking-card {
    border-color: #F44336;
}

.booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.booking-number {
    font-weight: 600;
    font-size: 15px;
}

.booking-card-body {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.empty-state {
    color: #8A8A8A;
    text-align: center;
    padding: 40px 0;
}

.empty-state a {
    color: #F44336;
    font-weight: 600;
}
</style>
<div class="container">
    <h2 class="section-title">My Bookings</h2>

    <?php if (count($bookings) === 0): ?>
        <p class="empty-state">You haven't made any bookings yet. <a href="browse-cameras.php">Browse cameras</a> to get started.</p>
    <?php else: ?>
        <div class="bookings-list">
            <?php foreach ($bookings as $b): ?>
                <a href="booking-confirmation.php?booking_id=<?php echo (int) $b['booking_id']; ?>" class="booking-card-link">
                    <div class="booking-card">
                        <div class="booking-card-header">
                            <span class="booking-number"><?php echo htmlspecialchars($b['booking_number']); ?></span>
                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $b['booking_status'])); ?>">
                                <?php echo htmlspecialchars($b['booking_status']); ?>
                            </span>
                        </div>
                        <div class="booking-card-body">
                            <div>
                                <span class="meta-label">Pickup</span>
                                <span class="meta-value"><?php echo htmlspecialchars($b['pickup_date']); ?></span>
                            </div>
                            <div>
                                <span class="meta-label">Return</span>
                                <span class="meta-value"><?php echo htmlspecialchars($b['return_date']); ?></span>
                            </div>
                            <div>
                                <span class="meta-label">Amount</span>
                                <span class="meta-value">&#8377;<?php echo number_format($b['total_amount'], 2); ?></span>
                            </div>
                            <?php if ($b['booking_status'] === 'Pending'): ?>
                        <form method="POST" action="cancel-booking.php" class="cancel-form" onclick="event.stopPropagation();" onsubmit="return confirm('Cancel this booking?');">
                            <input type="hidden" name="booking_id" value="<?php echo (int) $b['booking_id']; ?>">
                            <button type="submit" class="btn-secondary btn-small">Cancel Booking</button>
                        </form>
                        <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>