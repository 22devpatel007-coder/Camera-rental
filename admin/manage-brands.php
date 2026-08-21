<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT brand_id, brand_name, created_at FROM tbl_brands";
if ($search !== '') {
    $sql .= " WHERE brand_name LIKE ?";
}
$sql .= " ORDER BY brand_name";

$stmt = mysqli_prepare($conn, $sql);

if ($search !== '') {
    $like = '%' . $search . '%';
    mysqli_stmt_bind_param($stmt, 's', $like);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $r_brand_id, $r_brand_name, $r_created_at);

$brands = array();
while (mysqli_stmt_fetch($stmt)) {
    $brands[] = array(
        'brand_id'   => $r_brand_id,
        'brand_name' => $r_brand_name,
        'created_at' => $r_created_at
    );
}
mysqli_stmt_close($stmt);

$page_title = 'Manage Brands';
require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h2>Manage Brands</h2>
        <a href="add-brand.php" class="btn-primary">Add New Brand</a>
    </div>

    <form method="GET" action="manage-brands.php" class="filter-bar">
        <input type="text" name="search" placeholder="Search brand..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-primary">Search</button>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Brand Name</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($brands) === 0): ?>
                <tr><td colspan="4">No brands found.</td></tr>
            <?php else: ?>
                <?php foreach ($brands as $b): ?>
                    <tr>
                        <td><?php echo (int)$b['brand_id']; ?></td>
                        <td><?php echo htmlspecialchars($b['brand_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($b['created_at'])); ?></td>
                        <td class="admin-actions">
                            <a href="edit-brand.php?id=<?php echo $b['brand_id']; ?>">Edit</a>
                            <a href="delete-brand.php?id=<?php echo $b['brand_id']; ?>" class="delete-link" onclick="return confirm('Delete this brand?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>

<?php require_once '../includes/footer.php'; ?>