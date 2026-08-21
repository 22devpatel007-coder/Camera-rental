<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';

$camera_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($camera_id <= 0) {
    header('Location: manage-cameras.php');
    exit;
}

// Block delete if the camera has any booking history
$check = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM tbl_booking_items WHERE camera_id = ?");
mysqli_stmt_bind_param($check, 'i', $camera_id);
mysqli_stmt_execute($check);
mysqli_stmt_bind_result($check, $booking_count);
mysqli_stmt_fetch($check);
$has_bookings = $booking_count > 0;

if ($has_bookings) {
    header('Location: manage-cameras.php?error=has_bookings');
    exit;
}

// Get image filename before deleting the row
$stmt = mysqli_prepare($conn, "SELECT image FROM tbl_cameras WHERE camera_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $camera_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $img_name);
$img_found = mysqli_stmt_fetch($stmt);
$camera = $img_found ? array('image' => $img_name) : null;

if ($camera) {
    $delete = mysqli_prepare($conn, "DELETE FROM tbl_cameras WHERE camera_id = ?");
    mysqli_stmt_bind_param($delete, 'i', $camera_id);

    if (mysqli_stmt_execute($delete)) {
        $image_path = '../assets/uploads/cameras/' . $camera['image'];
        if (is_file($image_path)) {
            unlink($image_path);
        }
        header('Location: manage-cameras.php?deleted=1');
        exit;
    }
}

header('Location: manage-cameras.php');
exit;
?>