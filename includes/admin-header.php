<header class="admin-topbar">
    <div class="admin-topbar-title"><?php echo isset($page_title) ? htmlspecialchars($page_title) : ''; ?></div>
    <div class="admin-topbar-user">
        <?php echo htmlspecialchars($_SESSION['full_name']); ?>
    </div>
</header>
<div class="admin-layout">