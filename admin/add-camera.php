<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$error = '';
$success = '';

// Get brands and categories for dropdowns
$brands = mysqli_query($conn, "SELECT brand_id, brand_name FROM tbl_brands ORDER BY brand_name");
$categories = mysqli_query($conn, "SELECT category_id, category_name FROM tbl_categories ORDER BY category_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $camera_name = trim($_POST['camera_name']);
    $brand_id = (int)$_POST['brand_id'];
    $category_id = (int)$_POST['category_id'];
    $price_per_day = trim($_POST['price_per_day']);
    $price_per_hour = trim($_POST['price_per_hour']);
    $quantity = trim($_POST['quantity']);
    $description = trim($_POST['description']);

    // Basic validation
    if ($camera_name === '' || $brand_id <= 0 || $category_id <= 0 || $price_per_day === '' || $price_per_hour === '' || $quantity === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!is_numeric($price_per_day) || $price_per_day <= 0) {
        $error = 'Price per day must be a number greater than zero.';
    } elseif (!is_numeric($price_per_hour) || $price_per_hour <= 0) {
        $error = 'Price per hour must be a number greater than zero.';
    } elseif (!ctype_digit($quantity) || (int)$quantity < 0) {
        $error = 'Quantity must be a valid non-negative number.';
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a camera image.';
    } else {
        // Validate image type
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($file_type, $allowed_types)) {
            $error = 'Only JPG, JPEG, and PNG images are allowed.';
        } elseif ($file_size > $max_size) {
            $error = 'Image size must be under 2MB.';
        } else {
            // Generate safe unique filename
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            $new_filename = uniqid('cam_', true) . '.' . $ext;
            $upload_path = '../assets/images/cameras/' . $new_filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $stmt = mysqli_prepare($conn, "INSERT INTO tbl_cameras (brand_id, category_id, camera_name, price_per_day, price_per_hour, quantity, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Available')");
                mysqli_stmt_bind_param($stmt, 'iisddiss', $brand_id, $category_id, $camera_name, $price_per_day, $price_per_hour, $quantity, $description, $new_filename);

                if (mysqli_stmt_execute($stmt)) {
                    header('Location: manage-cameras.php?added=1');
                    exit;
                } else {
                    $error = 'Database error. Please try again.';
                }
            } else {
                $error = 'Failed to upload image. Please try again.';
            }
        }
    }
}

$page_title = 'Add Camera';
require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h2>Add New Camera</h2>
        <a href="manage-cameras.php" class="btn-primary">Back to List</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="form-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="add-camera.php" enctype="multipart/form-data" class="admin-form">
        <div class="form-group">
            <label for="camera_name">Camera Name</label>
            <input type="text" id="camera_name" name="camera_name" placeholder="e.g. Canon EOS 1500D" value="<?php echo isset($camera_name) ? htmlspecialchars($camera_name) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="brand_id">Brand</label>
            <select id="brand_id" name="brand_id" required>
                <option value="">Select Brand</option>
                <?php while ($b = mysqli_fetch_assoc($brands)): ?>
                    <option value="<?php echo $b['brand_id']; ?>"><?php echo htmlspecialchars($b['brand_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                <option value="">Select Category</option>
                <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['category_name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="price_per_day">Price Per Day (&#8377;)</label>
            <input type="number" id="price_per_day" name="price_per_day" step="0.01" min="0.01" placeholder="e.g. 500.00" required>
        </div>

        <div class="form-group">
            <label for="price_per_hour">Price Per Hour (&#8377;)</label>
            <input type="number" id="price_per_hour" name="price_per_hour" step="0.01" min="0.01" placeholder="e.g. 50.00" required>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" min="0" placeholder="e.g. 5" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Camera specifications and details"></textarea>
        </div>

        <div class="form-group">
            <label for="image">Camera Image</label>
            <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png" required>
        </div>

        <button type="submit" class="btn-primary">Add Camera</button>
    </form>
</div>
</div>

<?php require_once '../includes/footer.php'; ?>