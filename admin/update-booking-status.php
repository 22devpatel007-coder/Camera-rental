<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$valid_statuses = array('Pending', 'Approved', 'Picked Up', 'Returned', 'Cancelled');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = isset($_POST['booking_id']) ? (int) $_POST['booking_id'] : 0;
    $new_status = isset($_POST['booking_status']) ? trim($_POST['booking_status']) : '';

    if ($booking_id > 0 && in_array($new_status, $valid_statuses, true)) {
        // Lock the row and read current status first, so we know which transition this is.
        mysqli_query($conn, 'START TRANSACTION');

        $stmt = mysqli_prepare($conn, 'SELECT booking_status FROM tbl_bookings WHERE booking_id = ? FOR UPDATE');
        mysqli_stmt_bind_param($stmt, 'i', $booking_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $old_status);
        $row_found = mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($row_found) {
            // Stock was deducted once the booking reached Approved or later.
            $stock_was_deducted = in_array($old_status, array('Approved', 'Picked Up', 'Returned'), true);

            $should_decrement = ($old_status === 'Pending' && $new_status === 'Approved');
            $should_restore = ($stock_was_deducted && $new_status === 'Cancelled')
                            || ($old_status === 'Picked Up' && $new_status === 'Returned');

            if ($should_decrement) {
                adjust_camera_stock($conn, $booking_id, -1);
            } elseif ($should_restore) {
                adjust_camera_stock($conn, $booking_id, 1);
            }

            $update = mysqli_prepare($conn, 'UPDATE tbl_bookings SET booking_status = ? WHERE booking_id = ?');
            mysqli_stmt_bind_param($update, 'si', $new_status, $booking_id);
            mysqli_stmt_execute($update);
            mysqli_stmt_close($update);
        }

        mysqli_commit($conn);
    }
}

header('Location: manage-bookings.php');
exit;

/**
 * Increase or decrease camera stock for every item in a booking.
 * $direction: -1 to decrement (on Approved), 1 to restore (on Cancelled/Returned).
 *
 * @param mysqli $conn
 * @param int $booking_id
 * @param int $direction
 */
function adjust_camera_stock($conn, $booking_id, $direction) {
    $items_stmt = mysqli_prepare($conn, 'SELECT camera_id, quantity FROM tbl_booking_items WHERE booking_id = ?');
    mysqli_stmt_bind_param($items_stmt, 'i', $booking_id);
    mysqli_stmt_execute($items_stmt);
    mysqli_stmt_bind_result($items_stmt, $i_camera_id, $i_quantity);

    // Collect rows first since we can't run another prepared statement while this one is mid-fetch
    $items = array();
    while (mysqli_stmt_fetch($items_stmt)) {
        $items[] = array('camera_id' => $i_camera_id, 'quantity' => $i_quantity);
    }
    mysqli_stmt_close($items_stmt);

    $update_stmt = mysqli_prepare($conn, 'UPDATE tbl_cameras SET quantity = quantity + ? WHERE camera_id = ?');

    foreach ($items as $item) {
        $change = $direction * (int) $item['quantity'];
        mysqli_stmt_bind_param($update_stmt, 'ii', $change, $item['camera_id']);
        mysqli_stmt_execute($update_stmt);
    }
    mysqli_stmt_close($update_stmt);
}
?>