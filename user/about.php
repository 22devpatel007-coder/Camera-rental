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
            This Online Camera Rental System is developed as an academic project for a
            Bachelor of Computer Applications (BCA) course. It demonstrates the practical
            application of web development concepts including PHP, MySQL, and front-end
            technologies in building a functional e-commerce style rental platform.
        </p>

        <p>
            The system allows registered users to browse a catalog of cameras across
            different brands and categories, and rent equipment on a flexible basis —
            <strong>by the hour</strong> for short-term needs or <strong>by the day</strong>
            for longer shoots and projects. Pricing is calculated automatically based on
            the rental duration selected at checkout.
        </p>

        <h3>Project Objectives</h3>
        <ul>
            <li>Apply core PHP and MySQL concepts in a real-world style application</li>
            <li>Demonstrate secure coding practices such as prepared statements and input validation</li>
            <li>Build a complete booking workflow from browsing to checkout to admin approval</li>
            <li>Implement both hourly and daily rental pricing models</li>
        </ul>

        <h3>How Renting Works</h3>
        <ul>
            <li>Browse available cameras by brand or category</li>
            <li>Add cameras to your cart and choose a pickup and return date/time</li>
            <li>Rentals under 24 hours are billed hourly; 24 hours or more are billed daily</li>
            <li>Track your booking status from Pending through to Returned</li>
        </ul>

        <p>
            This project is intended solely for educational purposes and is not a
            commercial service.
        </p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>