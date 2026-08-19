<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$camera_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($camera_id <= 0) {
    header('Location: browse-cameras.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT c.camera_id, c.camera_name, c.price_per_day,c.price_per_hour, c.quantity,
                                       c.description, c.image, c.status,
                                       b.brand_name, cat.category_name
                                FROM tbl_cameras c
                                JOIN tbl_brands b ON c.brand_id = b.brand_id
                                JOIN tbl_categories cat ON c.category_id = cat.category_id
                                WHERE c.camera_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $camera_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $r_camera_id, $r_camera_name, $r_price_per_day, $r_price_per_hour, $r_quantity, $r_description, $r_image, $r_status, $r_brand_name, $r_category_name);
$found = mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

$camera = $found ? array(
    'camera_id' => $r_camera_id, 'camera_name' => $r_camera_name, 'price_per_day' => $r_price_per_day,
    'price_per_hour' => $r_price_per_hour, 'quantity' => $r_quantity, 'description' => $r_description,
    'image' => $r_image, 'status' => $r_status, 'brand_name' => $r_brand_name, 'category_name' => $r_category_name
) : null;
if (!$camera) {
    header('Location: browse-cameras.php');
    exit;
}

$page_title = htmlspecialchars($camera['camera_name']);
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <div class="camera-details">
        <div class="camera-details-image">
            <img src="<?php echo BASE_URL; ?>assets/images/cameras/<?php echo htmlspecialchars($camera['image']); ?>" alt="<?php echo htmlspecialchars($camera['camera_name']); ?>">
        </div>

        <div class="camera-details-info">
            <h2><?php echo htmlspecialchars($camera['camera_name']); ?></h2>
            <p class="camera-meta"><?php echo htmlspecialchars($camera['brand_name']); ?> &bull; <?php echo htmlspecialchars($camera['category_name']); ?></p>

            <p class="camera-price">&#8377;<?php echo number_format($camera['price_per_day'], 2); ?> / day</p>
            <p class="camera-price-hourly">&#8377;<?php echo number_format($camera['price_per_hour'], 2); ?> / hour</p>

            <p class="camera-status status-<?php echo strtolower($camera['status']); ?>">
                <?php echo htmlspecialchars($camera['status']); ?>
                <?php if ($camera['status'] === 'Available'): ?>
                    (<?php echo (int)$camera['quantity']; ?> in stock)
                <?php endif; ?>
            </p>

            <p class="camera-description"><?php echo nl2br(htmlspecialchars($camera['description'])); ?></p>

            <?php if ($camera['status'] === 'Available' && $camera['quantity'] > 0): ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" action="cart.php" class="inline-form">
                        <input type="hidden" name="camera_id" value="<?php echo (int) $camera['camera_id']; ?>">
                        <button type="submit" class="btn-secondary">Add to Cart</button>
                    </form>
                    <form method="POST" action="cart.php" class="inline-form">
                        <input type="hidden" name="camera_id" value="<?php echo (int) $camera['camera_id']; ?>">
                        <input type="hidden" name="rent_now" value="1">
                        <button type="submit" class="btn-primary">Rent Now</button>
                    </form>
                <?php else: ?>
                    <a href="../login.php" class="btn-primary">Login to Rent</a>
                <?php endif; ?>
            <?php else: ?>
                <button class="btn-primary" disabled>Currently Unavailable</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>