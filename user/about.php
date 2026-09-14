<?php
require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../includes/config.php';

$page_title = 'About Us';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<div class="container">
    <h2 class="section-title">About This Project</h2>

    <div class="about-content">
    <p>
        CameraHub is an online camera rental platform where you can browse, book, and rent
        professional camera gear without the hassle of long-term ownership. Whether you need
        a DSLR for a weekend shoot or a mirrorless kit for a longer project, we make renting
        simple and affordable.
    </p>

    <p>
        Choose from a wide range of brands and categories — DSLRs, mirrorless cameras, action
        cameras, and lenses — all listed with clear daily pricing, so you know exactly what
        you're paying before you book.
    </p>

    <h3>Why Rent With Us</h3>
    <ul>
        <li>Wide selection of cameras across top brands and categories</li>
        <li>Transparent daily pricing with no hidden charges</li>
        <li>Simple booking process from browsing to checkout</li>
        <li>Track every booking status from Pending to Returned</li>
    </ul>

    <h3>How Renting Works</h3>
    <ul>
        <li>Browse available cameras by brand or category</li>
        <li>Add your chosen camera to the cart and select pickup and return dates</li>
        <li>Total cost is calculated automatically based on rental days</li>
        <li>Once approved, pick up your gear and return it by the due date</li>
    </ul>

    <p>
        From casual photography to professional shoots, CameraHub connects you with the
        right equipment, right when you need it.
    </p>
</div>
</div>

<?php require_once '../includes/footer.php'; ?>