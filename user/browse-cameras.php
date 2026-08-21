<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

// Get filter values from URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Build query dynamically but safely (prepared statement)
$sql = "SELECT c.camera_id, c.camera_name, c.price_per_day, c.price_per_hour, c.image, c.status,
               b.brand_name, cat.category_name
        FROM tbl_cameras c
        JOIN tbl_brands b ON c.brand_id = b.brand_id
        JOIN tbl_categories cat ON c.category_id = cat.category_id
        WHERE c.status = 'Available'";

$params = array();
$types = '';

if ($search !== '') {
    $sql .= " AND c.camera_name LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}

if ($brand_id > 0) {
    $sql .= " AND c.brand_id = ?";
    $params[] = $brand_id;
    $types .= 'i';
}

if ($category_id > 0) {
    $sql .= " AND c.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

$sql .= " ORDER BY c.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($types !== '') {
    // Build a properly-referenced array for call_user_func_array.
    // mysqli_stmt_bind_param requires every argument (including $stmt) by reference.
    $bind_args = array();
    $bind_args[] = $stmt;
    $bind_args[] = $types;
    foreach ($params as $key => $value) {
        $bind_args[] = &$params[$key];
    }

    $ref_args = array();
    foreach ($bind_args as $key => $value) {
        $ref_args[$key] = &$bind_args[$key];
    }

    call_user_func_array('mysqli_stmt_bind_param', $ref_args);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $r_camera_id, $r_camera_name, $r_price_per_day, $r_price_per_hour, $r_image, $r_status, $r_brand_name, $r_category_name);

$cameras = array();
while (mysqli_stmt_fetch($stmt)) {
    $cameras[] = array(
        'camera_id'      => $r_camera_id,
        'camera_name'    => $r_camera_name,
        'price_per_day'  => $r_price_per_day,
        'price_per_hour' => $r_price_per_hour,
        'image'          => $r_image,
        'status'         => $r_status,
        'brand_name'     => $r_brand_name,
        'category_name'  => $r_category_name
    );
}
mysqli_stmt_close($stmt);

// Get all brands and categories for filter dropdowns
$brands = mysqli_query($conn, "SELECT brand_id, brand_name FROM tbl_brands ORDER BY brand_name");
$categories = mysqli_query($conn, "SELECT category_id, category_name FROM tbl_categories ORDER BY category_name");

$page_title = 'Browse Cameras';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <h2 class="section-title">Browse Cameras</h2>

    <form method="GET" action="browse-cameras.php" class="filter-bar">
        <input type="text" name="search" placeholder="Search camera..." value="<?php echo htmlspecialchars($search); ?>">

        <select name="brand_id">
            <option value="0">All Brands</option>
            <?php while ($b = mysqli_fetch_assoc($brands)): ?>
                <option value="<?php echo $b['brand_id']; ?>" <?php echo ($brand_id == $b['brand_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($b['brand_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="category_id">
            <option value="0">All Categories</option>
            <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                <option value="<?php echo $c['category_id']; ?>" <?php echo ($category_id == $c['category_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($c['category_name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit" class="btn-primary">Filter</button>
    </form>

    <div class="camera-grid">
        <?php if (count($cameras) === 0): ?>
            <p>No cameras found.</p>
        <?php else: ?>
            <?php foreach ($cameras as $cam): ?>
                <div class="camera-card">
                    <img src="<?php echo BASE_URL; ?>assets/images/cameras/<?php echo htmlspecialchars($cam['image']); ?>" alt="<?php echo htmlspecialchars($cam['camera_name']); ?>">
                    <h3><?php echo htmlspecialchars($cam['camera_name']); ?></h3>
                    <p><?php echo htmlspecialchars($cam['brand_name']); ?> &bull; <?php echo htmlspecialchars($cam['category_name']); ?></p>
                    <p class="price">&#8377;<?php echo number_format($cam['price_per_day'], 2); ?> / day &middot; &#8377;<?php echo number_format($cam['price_per_hour'], 2); ?> / hr</p>
                    <div class="camera-card-actions">
                        <a href="camera-details.php?id=<?php echo $cam['camera_id']; ?>" class="btn-secondary">View Details</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form method="POST" action="cart.php" class="inline-form">
                                <input type="hidden" name="camera_id" value="<?php echo (int) $cam['camera_id']; ?>">
                                <button type="submit" class="btn-secondary btn-small">Add to Cart</button>
                            </form>
                            <form method="POST" action="cart.php" class="inline-form">
                                <input type="hidden" name="camera_id" value="<?php echo (int) $cam['camera_id']; ?>">
                                <input type="hidden" name="rent_now" value="1">
                                <button type="submit" class="btn-primary btn-small">Rent Now</button>
                            </form>
                        <?php else: ?>
                            <a href="../login.php" class="btn-primary">Login to Rent</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>