// Scroll-reveal animation controller
// Uses IntersectionObserver for performance (no scroll event listeners).
document.addEventListener('DOMContentLoaded', function () {

    // ---- Hero load-in: image first, then text (runs once, on page load) ----
    var heroImage = document.querySelector('.hero-load-image');
    var heroText = document.querySelector('.hero-load-text');

    if (heroImage) { heroImage.classList.add('is-loaded'); }
    if (heroText) { heroText.classList.add('is-loaded'); }

    // ---- Scroll reveal for the rest of the sections ----
    var targets = document.querySelectorAll('.reveal, .reveal-stagger');

    if (!('IntersectionObserver' in window) || targets.length === 0) {
        // Fallback: show everything immediately on old browsers
        for (var i = 0; i < targets.length; i++) {
            targets[i].classList.add('is-visible');
        }
        return;
    }

    var observer = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    });

    targets.forEach(function (el) {
        observer.observe(el);
    });
});