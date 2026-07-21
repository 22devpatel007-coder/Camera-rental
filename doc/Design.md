---

# Technical Compatibility

This project is designed specifically for college use and must remain fully compatible with older PHP environments.

## Target Environment

- PHP 5.5 or lower
- WAMP Server 5.x
- MySQL
- MySQLi Extension
- Apache

The entire project must run without requiring Composer, frameworks, or any modern PHP features.

---

# Development Rules

Use only:

- HTML5
- CSS3
- Vanilla JavaScript
- PHP
- MySQL
- MySQLi Extension

Do not use:

- Laravel
- CodeIgniter
- Symfony
- Composer
- Bootstrap
- Tailwind CSS
- jQuery
- React
- Vue
- Angular
- Node.js

---

# PHP Compatibility Rules

The code must remain compatible with PHP 5.5 or lower.

### Allowed

- MySQLi
- include / require
- Sessions
- Functions
- Arrays using `array()`
- Prepared Statements
- File Uploads
- md5() for password hashing (for compatibility)

### Do Not Use

- password_hash()
- password_verify()
- Spaceship Operator (`<=>`)
- Null Coalescing Operator (`??`)
- Arrow Functions
- Anonymous Classes
- Scalar Type Hints
- Return Type Declarations
- Namespaces
- Traits
- Short Array Syntax if maximum compatibility is required
- Composer Autoloading

All code should execute correctly on PHP 5.5 or lower without modification.

---

# Coding Style

- Keep code simple and beginner-friendly.
- Write readable and well-commented PHP.
- Reuse common components from the `includes` folder.
- Avoid duplicate code.
- Use meaningful variable and function names.
- Separate HTML, PHP, CSS, and JavaScript whenever possible.
- Use consistent indentation throughout the project.

---

# Security Guidelines

Although this is a college project, basic security practices should still be followed.

- Use prepared statements for database queries.
- Validate all user input.
- Escape output using `htmlspecialchars()`.
- Validate uploaded image types and sizes.
- Use sessions for authentication.
- Prevent direct access to protected pages.
- Destroy sessions properly during logout.
- Use `md5()` only because the project targets PHP 5.5 or lower and compatibility is required. In real-world applications, `password_hash()` and `password_verify()` should always be used instead.

---