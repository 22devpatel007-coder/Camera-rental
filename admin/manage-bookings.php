<?php
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

$valid_statuses = array('Pending', 'Approved', 'Picked Up', 'Returned', 'Cancelled');

$sql = "SELECT b.booking_id, b.booking_number, b.pickup_date, b.return_date, b.total_amount, b.booking_status, b.booking_date,
               u.full_name, u.email
        FROM tbl_bookings b
        JOIN tbl_users u ON b.user_id = u.user_id
        WHERE 1=1";

$params = array();
$types = '';

if ($search !== '') {
    $sql .= " AND (b.booking_number LIKE ? OR u.full_name LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if ($status_filter !== '' && in_array($status_filter, $valid_statuses, true)) {
    $sql .= " AND b.booking_status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

$sql .= " ORDER BY b.booking_date DESC";

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
mysqli_stmt_bind_result($stmt, $r_booking_id, $r_booking_number, $r_pickup_date, $r_return_date, $r_total_amount, $r_booking_status, $r_booking_date, $r_full_name, $r_email);

// Fetch into a plain array so the display loop doesn't depend on the fetch method
$bookings = array();
while (mysqli_stmt_fetch($stmt)) {
    $bookings[] = array(
        'booking_id'     => $r_booking_id,
        'booking_number' => $r_booking_number,
        'pickup_date'    => $r_pickup_date,
        'return_date'    => $r_return_date,
        'total_amount'   => $r_total_amount,
        'booking_status' => $r_booking_status,
        'booking_date'   => $r_booking_date,
        'full_name'      => $r_full_name,
        'email'          => $r_email
    );
}
mysqli_stmt_close($stmt);

$page_title = 'Manage Bookings';
require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h2>Manage Bookings</h2>
    </div>

    <form method="GET" action="manage-bookings.php" class="filter-bar">
        <input type="text" name="search" placeholder="Search by booking # or customer..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="status">
            <option value="">All Statuses</option>
            <?php foreach ($valid_statuses as $status): ?>
                <option value="<?php echo $status; ?>" <?php echo ($status_filter === $status) ? 'selected' : ''; ?>><?php echo $status; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-primary">Filter</button>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Booking #</th>
                <th>Customer</th>
                <th>Pickup</th>
                <th>Return</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($bookings) === 0): ?>
                <tr><td colspan="7">No bookings found.</td></tr>
            <?php else: ?>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($b['booking_number']); ?></td>
                        <td><?php echo htmlspecialchars($b['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($b['pickup_date']); ?></td>
                        <td><?php echo htmlspecialchars($b['return_date']); ?></td>
                        <td>&#8377;<?php echo number_format($b['total_amount'], 2); ?></td>
                        <td><span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $b['booking_status'])); ?>"><?php echo htmlspecialchars($b['booking_status']); ?></span></td>
                         <td class="admin-actions">
                            <form method="POST" action="update-booking-status.php" class="status-form">
                                <input type="hidden" name="booking_id" value="<?php echo (int) $b['booking_id']; ?>">
                                <select name="booking_status">
                                    <?php foreach ($valid_statuses as $status): ?>
                                        <option value="<?php echo $status; ?>" <?php echo ($b['booking_status'] === $status) ? 'selected' : ''; ?>><?php echo $status; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn-primary btn-small">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>

<?php require_once '../includes/footer.php'; ?>