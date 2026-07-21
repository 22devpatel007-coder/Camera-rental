<?php
require_once 'includes/session.php';
require_once 'includes/database.php';
require_once 'includes/config.php';

$page_title = 'Home';
require_once 'includes/header.php';
require_once 'includes/navbar.php';


?>

<!-- ===== Hero / Featured Showcase Section (static) ===== -->
<section class="showcase">
    <div class="container showcase-inner">
        <div class="showcase-text">
            <h1 class="showcase-title">Canon EOS<br>5D Mark IV</h1>
            <p class="showcase-desc">
                A full-frame DSLR built for serious photography and filmmaking.
                Rent it with a versatile lens kit and start shooting today.
            </p>
            <a href="<?php echo BASE_URL; ?>user/browse-cameras.php" class="btn-primary showcase-btn">Browse Cameras</a>
        </div>
        <div class="showcase-image">
            <img src="<?php echo BASE_URL; ?>assets/images/hero/hero-1.jpg" alt="Canon EOS 5D Mark IV">
        </div>
    </div>
</section>

<!-- ===== Lens / Secondary Showcase Section (static) ===== -->
<section class="section lens-showcase">
    <div class="container lens-showcase-inner">
        <div class="lens-image">
            <img src="<?php echo BASE_URL; ?>assets/images/hero/hero-2.jpg" alt="Featured lens">
        </div>
        <div class="lens-text">
            <h2 class="section-title">EF24-105mm f/4L IS II USM</h2>
            <p>
                A versatile standard zoom lens covering wide-angle to mid-telephoto shots,
                built for sharp, reliable results in almost any shooting condition.
            </p>
            <a href="<?php echo BASE_URL; ?>user/browse-cameras.php" class="link-more">read more...</a>
        </div>
    </div>
</section>

<!-- ===== Categories Section (static, large tiles) ===== -->
<!-- <section class="section categories-section">
    <div class="container">
        <h2 class="section-title">Browse by Category</h2>
        <p class="section-subtitle">Find the right gear for your next shoot</p>

        <div class="category-tiles">
            <a href="<?php echo BASE_URL; ?>user/browse-cameras.php?category=dslr" class="category-tile" style="background-image: url('<?php echo BASE_URL; ?>assets/images/icons/dslr.jpg');">
                <span class="category-tile-label">DSLR</span>
            </a>
            <a href="<?php echo BASE_URL; ?>user/browse-cameras.php?category=mirrorless" class="category-tile" style="background-image: url('<?php echo BASE_URL; ?>assets/images/icons/mirrorless.jpg');">
                <span class="category-tile-label">Mirrorless</span>
            </a>
            <a href="<?php echo BASE_URL; ?>user/browse-cameras.php?category=action" class="category-tile" style="background-image: url('<?php echo BASE_URL; ?>assets/images/icons/action-camera.jpg');">
                <span class="category-tile-label">Action Camera</span>
            </a>
            <a href="<?php echo BASE_URL; ?>user/browse-cameras.php?category=drone" class="category-tile" style="background-image: url('<?php echo BASE_URL; ?>assets/images/icons/drone.jpg');">
                <span class="category-tile-label">Drone</span>
            </a>
            <a href="<?php echo BASE_URL; ?>user/browse-cameras.php?category=lens" class="category-tile" style="background-image: url('<?php echo BASE_URL; ?>assets/images/icons/lens.jpg');">
                <span class="category-tile-label">Lens</span>
            </a>
        </div>
    </div>
</section> -->

<!-- ===== About Preview Section ===== -->
<section class="section about-preview">
    <div class="container about-preview-inner">
        <div class="about-preview-text">
            <h2 class="section-title">About Us</h2>
            <p>
                We make professional photography and videography gear accessible to everyone.
                Rent DSLRs, mirrorless cameras, lenses, drones and accessories without the
                cost of owning them outright — perfect for projects, events, and short shoots.
            </p>
            <a href="<?php echo BASE_URL; ?>user/about.php" class="link-more">read more...</a>
        </div>
    </div>
</section>

<!-- ===== Contact Preview Section ===== -->
<section class="section contact-preview">
    <div class="container contact-preview-inner">
        <h2 class="section-title">Get In Touch</h2>
        <p class="section-subtitle">Have a question about a booking or a camera? We're happy to help.</p>
        <a href="<?php echo BASE_URL; ?>user/contact.php" class="btn-primary contact-btn">Contact Us</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>