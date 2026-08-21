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

// ===== Email & Password Validation (Login / Signup) =====

document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('form');

    for (var i = 0; i < forms.length; i++) {
        forms[i].addEventListener('submit', function (e) {
            var form = e.target;
            var email = form.querySelector('input[name="email"]');
            var password = form.querySelector('input[name="password"]');
            var confirmPassword = form.querySelector('input[name="confirm_password"]');
            var errorMsg = '';

            if (email && !isValidEmail(email.value.trim())) {
                errorMsg = 'Please enter a valid email address.';
            } else if (password && password.value.length < 6) {
                errorMsg = 'Password must be at least 6 characters.';
            } else if (confirmPassword && confirmPassword.value !== password.value) {
                errorMsg = 'Passwords do not match.';
            }

            if (errorMsg !== '') {
                e.preventDefault();
                showClientError(form, errorMsg);
            }
        });
    }

    function isValidEmail(value) {
        var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(value);
    }

    function showClientError(form, message) {
        var existing = form.parentNode.querySelector('.form-error');
        if (existing) {
            existing.textContent = message;
            return;
        }
        var div = document.createElement('div');
        div.className = 'form-error';
        div.textContent = message;
        form.parentNode.insertBefore(div, form);
    }
});