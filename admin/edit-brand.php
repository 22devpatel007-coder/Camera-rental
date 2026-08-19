<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$brand_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($brand_id <= 0) {
    header('Location: manage-brands.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT brand_id, brand_name FROM tbl_brands WHERE brand_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $brand_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $eb_brand_id, $eb_brand_name);
$brand_found = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
$brand = $brand_found ? array('brand_id' => $eb_brand_id, 'brand_name' => $eb_brand_name) : null;

if (!$brand) {
    header('Location: manage-brands.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_name = trim($_POST['brand_name']);

    if ($brand_name === '') {
        $error = 'Brand name is required.';
    } else {
        // Check for duplicate name on a different brand
        $check = mysqli_prepare($conn, "SELECT brand_id FROM tbl_brands WHERE brand_name = ? AND brand_id != ?");
        mysqli_stmt_bind_param($check, 'si', $brand_name, $brand_id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        $duplicate_found = mysqli_stmt_num_rows($check) > 0;
        mysqli_stmt_close($check);

        if ($duplicate_found) {
            $error = 'Another brand already uses this name.';
        } else {
            $update = mysqli_prepare($conn, "UPDATE tbl_brands SET brand_name = ? WHERE brand_id = ?");
            mysqli_stmt_bind_param($update, 'si', $brand_name, $brand_id);

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);
                header('Location: manage-brands.php?updated=1');
                exit;
            } else {
                $error = 'Database error. Please try again.';
                mysqli_stmt_close($update);
            }
        }
    }

    $brand['brand_name'] = $brand_name;
}

$page_title = 'Edit Brand';
require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h2>Edit Brand</h2>
        <a href="manage-brands.php" class="btn-primary">Back to List</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="form-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="edit-brand.php?id=<?php echo $brand_id; ?>" class="admin-form">
        <div class="form-group">
            <label for="brand_name">Brand Name</label>
            <input type="text" id="brand_name" name="brand_name" value="<?php echo htmlspecialchars($brand['brand_name']); ?>" required>
        </div>

        <button type="submit" class="btn-primary">Update Brand</button>
    </form>
</div>
</div>

<?php require_once '../includes/footer.php'; ?>