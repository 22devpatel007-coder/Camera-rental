// ===== Mobile Navbar Toggle =====
document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.getElementById('navbarToggle');
    var navLinks = document.getElementById('navbarLinks');

    if (toggleBtn && navLinks) {
        toggleBtn.addEventListener('click', function () {
            navLinks.classList.toggle('active');
        });
    }
});