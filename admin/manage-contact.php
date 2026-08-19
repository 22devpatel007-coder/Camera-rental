<?php
require_once '../includes/session.php';
require_once '../includes/admin-auth.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$page_title = 'Manage Contact Messages';

$query = "SELECT contact_id, name, email, subject, message, created_at FROM tbl_contact ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$count = $result ? mysqli_num_rows($result) : 0;

require_once '../includes/header.php';
require_once '../includes/admin-header.php';
require_once '../includes/admin-sidebar.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <h2><?php echo htmlspecialchars($page_title); ?></h2>
    </div>

    <?php if (!$result): ?>
        <p class="no-data">Error loading messages.</p>
    <?php else: ?>

        <div class="contact-stats">
            <p>Total Messages: <strong><?php echo $count; ?></strong></p>
        </div>

        <?php if ($count > 0): ?>
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo (int) $row['contact_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td class="message-cell"><?php echo htmlspecialchars($row['message']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-data">No contact messages yet.</p>
        <?php endif; ?>

    <?php endif; ?>
</div>
</div>
<?php require_once '../includes/footer.php'; ?>