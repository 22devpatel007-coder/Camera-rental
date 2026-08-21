<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$camera_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($camera_id <= 0) {
    header('Location: manage-cameras.php');
    exit;
}

$error = '';

// Fetch existing camera data (explicit columns instead of SELECT * so bind_result can map them)
$stmt = mysqli_prepare($conn, "SELECT camera_id, brand_id, category_id, camera_name, price_per_day, price_per_hour, quantity, description, image, status FROM tbl_cameras WHERE camera_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $camera_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $ec_camera_id, $ec_brand_id, $ec_category_id, $ec_camera_name, $ec_price_per_day, $ec_price_per_hour, $ec_quantity, $ec_description, $ec_image, $ec_status);
$camera_found = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$camera_found) {
    header('Location: manage-cameras.php');
    exit;
}

$camera = array(
    'camera_id'      => $ec_camera_id,
    'brand_id'       => $ec_brand_id,
    'category_id'    => $ec_category_id,
    'camera_name'    => $ec_camera_name,
    'price_per_day'  => $ec_price_per_day,
    'price_per_hour' => $ec_price_per_hour,
    'quantity'       => $ec_quantity,
    'description'    => $ec_description,
    'image'          => $ec_image,
    'status'         => $ec_status
);

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
    $status = $_POST['status'];
    $image_name = $camera['image']; // keep existing by default

    $valid_statuses = array('Available', 'Booked', 'Maintenance', 'Unavailable');

    if ($camera_name === '' || $brand_id <= 0 || $category_id <= 0 || $price_per_day === '' || $price_per_hour === '' || $quantity === '') {
        $error = 'Please fill in all required fields.';
    } elseif (!is_numeric($price_per_day) || $price_per_day <= 0) {
        $error = 'Price per day must be a number greater than zero.';
    } elseif (!is_numeric($price_per_hour) || $price_per_hour <= 0) {
        $error = 'Price per hour must be a number greater than zero.';
    } elseif (!ctype_digit($quantity) || (int)$quantity < 0) {
        $error = 'Quantity must be a valid non-negative number.';
    } elseif (!in_array($status, $valid_statuses)) {
        $error = 'Invalid status selected.';
    } else {
        // Handle optional new image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];
            $max_size = 2 * 1024 * 1024;

            if (!in_array($file_type, $allowed_types)) {
                $error = 'Only JPG, JPEG, and PNG images are allowed.';
            } elseif ($file_size > $max_size) {
                $error = 'Image size must be under 2MB.';
            } else {
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $new_filename = uniqid('cam_', true) . '.' . $ext;
                $upload_path = '../assets/uploads/cameras/' . $new_filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Delete old image if it exists in uploads folder
                    $old_path = '../assets/uploads/cameras/' . $camera['image'];
                    if (is_file($old_path)) {
                        unlink($old_path);
                    }
                    $image_name = $new_filename;
                } else {
                    $error = 'Failed to upload new image.';
                }
            }
        }

        if ($error === '') {
            $update = mysqli_prepare($conn, "UPDATE tbl_cameras SET brand_id = ?, category_id = ?, camera_name = ?, price_per_day = ?, price_per_hour = ?, quantity = ?, description = ?, image = ?, status = ? WHERE camera_id = ?");
            mysqli_stmt_bind_param($update, 'iisddisssi', $brand_id, $category_id, $camera_name, $price_per_day, $price_per_hour, $quantity, $description, $image_name, $status, $camera_id);

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);
                header('Location: manage-cameras.php?updated=1');
                exit;
            } else {
                $error = 'Database error. Please try again.';
                mysqli_stmt_close($update);
            }
        }
    }

    // Keep submitted values on error (except image)
    $camera['camera_name'] = $camera_name;
    $camera['brand_id'] = $brand_id;
    $camera['category_id'] = $category_id;
    $camera['price_per_day'] = $price_per_day;
    $camera['price_per_hour'] = $price_per_hour;
    $camera['quantity'] = $quantity;
    $camera['description'] = $description;
    $camera['status'] = $status;
}

$page_title = 'Edit Camera';
require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h2>Edit Camera</h2>
        <a href="manage-cameras.php" class="btn-primary">Back to List</a>
    </div>

    <?php if ($error !== ''): ?>
        <div class="form-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="edit-camera.php?id=<?php echo $camera_id; ?>" enctype="multipart/form-data" class="admin-form">
        <div class="form-group">
            <label>Current Image</label>
            <div class="current-image">
                <img src="<?php echo BASE_URL; ?>assets/uploads/cameras/<?php echo htmlspecialchars($camera['image']); ?>" alt="<?php echo htmlspecialchars($camera['camera_name']); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="camera_name">Camera Name</label>
            <input type="text" id="camera_name" name="camera_name" value="<?php echo htmlspecialchars($camera['camera_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="brand_id">Brand</label>
            <select id="brand_id" name="brand_id" required>
                <?php while ($b = mysqli_fetch_assoc($brands)): ?>
                    <option value="<?php echo $b['brand_id']; ?>" <?php echo ($b['brand_id'] == $camera['brand_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($b['brand_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?php echo $c['category_id']; ?>" <?php echo ($c['category_id'] == $camera['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="price_per_day">Price Per Day (&#8377;)</label>
            <input type="number" id="price_per_day" name="price_per_day" step="0.01" min="0.01" value="<?php echo htmlspecialchars($camera['price_per_day']); ?>" required>
        </div>

        <div class="form-group">
            <label for="price_per_hour">Price Per Hour (&#8377;)</label>
            <input type="number" id="price_per_hour" name="price_per_hour" step="0.01" min="0.01" value="<?php echo htmlspecialchars($camera['price_per_hour']); ?>" required>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" id="quantity" name="quantity" min="0" value="<?php echo htmlspecialchars($camera['quantity']); ?>" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="Available" <?php echo ($camera['status'] === 'Available') ? 'selected' : ''; ?>>Available</option>
                <option value="Booked" <?php echo ($camera['status'] === 'Booked') ? 'selected' : ''; ?>>Booked</option>
                <option value="Maintenance" <?php echo ($camera['status'] === 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                <option value="Unavailable" <?php echo ($camera['status'] === 'Unavailable') ? 'selected' : ''; ?>>Unavailable</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($camera['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Replace Image (optional)</label>
            <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png">
        </div>

        <button type="submit" class="btn-primary">Update Camera</button>
    </form>
</div>
</div>

<?php require_once '../includes/footer.php'; ?>