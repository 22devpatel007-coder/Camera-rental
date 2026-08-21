<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT c.camera_id, c.camera_name, c.price_per_day, c.quantity, c.status,
               b.brand_name, cat.category_name
        FROM tbl_cameras c
        JOIN tbl_brands b ON c.brand_id = b.brand_id
        JOIN tbl_categories cat ON c.category_id = cat.category_id";

if ($search !== '') {
    $sql .= " WHERE c.camera_name LIKE ?";
}
$sql .= " ORDER BY c.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($search !== '') {
    $like = '%' . $search . '%';
    mysqli_stmt_bind_param($stmt, 's', $like);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $r_camera_id, $r_camera_name, $r_price_per_day, $r_quantity, $r_status, $r_brand_name, $r_category_name);

$cameras = array();
while (mysqli_stmt_fetch($stmt)) {
    $cameras[] = array(
        'camera_id'     => $r_camera_id,
        'camera_name'   => $r_camera_name,
        'price_per_day' => $r_price_per_day,
        'quantity'      => $r_quantity,
        'status'        => $r_status,
        'brand_name'    => $r_brand_name,
        'category_name' => $r_category_name
    );
}
mysqli_stmt_close($stmt);

$page_title = 'Manage Cameras';
require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h2>Manage Cameras</h2>
        <a href="add-camera.php" class="btn-primary">Add New Camera</a>
    </div>
    <?php if (isset($_GET['error']) && $_GET['error'] === 'has_bookings'): ?>
    <div class="form-error">Cannot delete this camera because it has booking history. Set its status to Unavailable instead.</div>
    <?php endif; ?>
    <form method="GET" action="manage-cameras.php" class="filter-bar">
        <input type="text" name="search" placeholder="Search camera..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-primary">Search</button>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Price/Day</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($cameras) === 0): ?>
                <tr><td colspan="8">No cameras found.</td></tr>
            <?php else: ?>
                <?php foreach ($cameras as $cam): ?>
                    <tr>
                        <td><?php echo (int)$cam['camera_id']; ?></td>
                        <td><?php echo htmlspecialchars($cam['camera_name']); ?></td>
                        <td><?php echo htmlspecialchars($cam['brand_name']); ?></td>
                        <td><?php echo htmlspecialchars($cam['category_name']); ?></td>
                        <td>&#8377;<?php echo number_format($cam['price_per_day'], 2); ?></td>
                        <td><?php echo (int)$cam['quantity']; ?></td>
                        <td><span class="status-badge status-<?php echo strtolower($cam['status']); ?>"><?php echo htmlspecialchars($cam['status']); ?></span></td>
                        <td class="admin-actions">
                            <a href="edit-camera.php?id=<?php echo $cam['camera_id']; ?>">Edit</a>
                            <a href="delete-camera.php?id=<?php echo $cam['camera_id']; ?>" class="delete-link" onclick="return confirm('Delete this camera?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>

<?php require_once '../includes/footer.php'; ?>