<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

function get_bookings_by_status($conn, $status) {
    $stmt = mysqli_prepare($conn, "SELECT b.booking_id, b.booking_number, b.pickup_date, b.return_date, b.total_amount, u.full_name
                                    FROM tbl_bookings b
                                    JOIN tbl_users u ON b.user_id = u.user_id
                                    WHERE b.booking_status = ?
                                    ORDER BY b.booking_date DESC");
    mysqli_stmt_bind_param($stmt, 's', $status);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $r_booking_id, $r_booking_number, $r_pickup_date, $r_return_date, $r_total_amount, $r_full_name);

    // Drain into a plain array immediately, since bind_result is unbuffered
    // and we need to run other statements before rendering.
    $rows = array();
    while (mysqli_stmt_fetch($stmt)) {
        $rows[] = array(
            'booking_id'     => $r_booking_id,
            'booking_number' => $r_booking_number,
            'pickup_date'    => $r_pickup_date,
            'return_date'    => $r_return_date,
            'total_amount'   => $r_total_amount,
            'full_name'      => $r_full_name
        );
    }
    mysqli_stmt_close($stmt);

    return $rows;
}

$active = get_bookings_by_status($conn, 'Picked Up');
$returned = get_bookings_by_status($conn, 'Returned');
$cancelled = get_bookings_by_status($conn, 'Cancelled');

$page_title = 'Rent Status';
require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';

function render_rental_table($rows, $empty_message) {
?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Booking No.</th>
                <th>Customer</th>
                <th>Pickup</th>
                <th>Return</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($rows) === 0): ?>
                <tr><td colspan="5"><?php echo htmlspecialchars($empty_message); ?></td></tr>
            <?php else: ?>
                <?php foreach ($rows as $b): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($b['booking_number']); ?></td>
                        <td><?php echo htmlspecialchars($b['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($b['pickup_date']); ?></td>
                        <td><?php echo htmlspecialchars($b['return_date']); ?></td>
                        <td>&#8377;<?php echo number_format($b['total_amount'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
<?php
}
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h2>Rent Status</h2>
    </div>

    <h3>Active Rentals</h3>
    <?php render_rental_table($active, 'No active rentals.'); ?>

    <h3>Returned Rentals</h3>
    <?php render_rental_table($returned, 'No returned rentals.'); ?>

    <h3>Cancelled Rentals</h3>
    <?php render_rental_table($cancelled, 'No cancelled rentals.'); ?>
</div>
</div>
<?php require_once '../includes/footer.php'; ?>