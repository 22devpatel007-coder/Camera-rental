<?php
require_once '../includes/user-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = isset($_POST['booking_id']) ? (int) $_POST['booking_id'] : 0;

    if ($booking_id > 0) {
        // Only allow cancelling bookings that belong to this user AND are still Pending
        $stmt = mysqli_prepare($conn, "UPDATE tbl_bookings SET booking_status = 'Cancelled' WHERE booking_id = ? AND user_id = ? AND booking_status = 'Pending'");
        mysqli_stmt_bind_param($stmt, 'ii', $booking_id, $user_id);
        mysqli_stmt_execute($stmt);
    }
}

header('Location: my-bookings.php');
exit;