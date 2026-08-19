<?php
require_once '../includes/session.php';
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT user_id, full_name, email, phone, created_at FROM tbl_users WHERE role = 'user'";

if ($search !== '') {
    $sql .= " AND (full_name LIKE ? OR email LIKE ?)";
}
$sql .= " ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

if ($search !== '') {
    $like = '%' . $search . '%';
    mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $r_user_id, $r_full_name, $r_email, $r_phone, $r_created_at);

// Build a plain array so the display loop below doesn't need to know how the fetch happened
$customers = array();
while (mysqli_stmt_fetch($stmt)) {
    $customers[] = array(
        'user_id'    => $r_user_id,
        'full_name'  => $r_full_name,
        'email'      => $r_email,
        'phone'      => $r_phone,
        'created_at' => $r_created_at
    );
}
mysqli_stmt_close($stmt);

$page_title = 'Manage Customers';
require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h2>Manage Customers</h2>
    </div>

    <form method="GET" action="manage-customers.php" class="filter-bar">
        <input type="text" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-primary">Search</button>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($customers) === 0): ?>
                <tr><td colspan="6">No customers found.</td></tr>
            <?php else: ?>
                <?php foreach ($customers as $cust): ?>
                    <tr>
                        <td><?php echo (int) $cust['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($cust['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($cust['email']); ?></td>
                        <td><?php echo htmlspecialchars($cust['phone']); ?></td>
                        <td><?php echo htmlspecialchars($cust['created_at']); ?></td>
                        <td class="admin-actions">
                            <a href="customer-details.php?id=<?php echo (int) $cust['user_id']; ?>">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>
<?php require_once '../includes/footer.php'; ?>