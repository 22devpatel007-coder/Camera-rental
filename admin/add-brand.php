<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_name = trim($_POST['brand_name']);

    if ($brand_name === '') {
        $error = 'Brand name is required.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT brand_id FROM tbl_brands WHERE brand_name = ?");
        mysqli_stmt_bind_param($stmt, 's', $brand_name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = 'This brand already exists.';
        } else {
            $insert = mysqli_prepare($conn, "INSERT INTO tbl_brands (brand_name) VALUES (?)");
            mysqli_stmt_bind_param($insert, 's', $brand_name);

            if (mysqli_stmt_execute($insert)) {
                header('Location: manage-brands.php?added=1');
                exit;
            } else {
                $error = 'Database error. Please try again.';
            }
        }
    }
}

$page_title = 'Add Brand';
require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h2>Add New Brand</h2>
        <a href="manage-brands.php" class="btn-primary">Back to List</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="form-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="add-brand.php" class="admin-form">
        <div class="form-group">
            <label for="brand_name">Brand Name</label>
            <input type="text" id="brand_name" name="brand_name" placeholder="e.g. Canon" value="<?php echo isset($brand_name) ? htmlspecialchars($brand_name) : ''; ?>" required>
        </div>

        <button type="submit" class="btn-primary">Add Brand</button>
    </form>
</div>
</div>

<?php require_once '../includes/footer.php'; ?>