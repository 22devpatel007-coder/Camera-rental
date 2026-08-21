<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';

$brand_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($brand_id <= 0) {
    header('Location: manage-brands.php');
    exit;
}

// Check if any cameras use this brand
$check = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM tbl_cameras WHERE brand_id = ?");
mysqli_stmt_bind_param($check, 'i', $brand_id);
mysqli_stmt_execute($check);
mysqli_stmt_bind_result($check, $count);
mysqli_stmt_fetch($check);

if ($count > 0) {
    header('Location: manage-brands.php?error=inuse');
    exit;
}

$delete = mysqli_prepare($conn, "DELETE FROM tbl_brands WHERE brand_id = ?");
mysqli_stmt_bind_param($delete, 'i', $brand_id);

if (mysqli_stmt_execute($delete)) {
    header('Location: manage-brands.php?deleted=1');
} else {
    header('Location: manage-brands.php?error=1');
}
exit;
?>