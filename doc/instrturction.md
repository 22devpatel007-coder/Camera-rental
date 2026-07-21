# Development Instructions

## Project Overview

Build this project as a **college submission**, not as a commercial application.

The primary goals are:

- Clean code
- Professional UI
- Reusable components
- Simple architecture
- Easy maintenance
- Full compatibility with older PHP environments

---

# Target Environment

The project **must** run correctly on the following environment without modification.

- WAMP Server 5.2 or lower
- PHP 5.5 or lower
- Apache
- MySQL
- MySQLi Extension

No modern PHP features should be required.

---

# Technology Stack

Use only:

- PHP
- MySQL
- HTML5
- CSS3
- Vanilla JavaScript

---

# Framework Restrictions

Do NOT use:

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
- npm packages
- External UI libraries
- CSS frameworks
- JavaScript frameworks

Everything must be built using only native PHP, HTML, CSS, JavaScript, and MySQL.

---

# PHP Compatibility Rules

The project must remain compatible with **PHP 5.5 or lower**.

## Allowed

- MySQLi
- include
- include_once
- require
- require_once
- Sessions
- Functions
- Arrays
- Prepared Statements
- File Uploads
- md5() for password hashing (compatibility only)

## Do Not Use

- password_hash()
- password_verify()
- Traits
- Namespaces
- Composer Autoloading
- Arrow Functions
- Anonymous Classes
- Scalar Type Hints
- Return Type Declarations
- Null Coalescing Operator (`??`)
- Spaceship Operator (`<=>`)
- Any PHP feature introduced after PHP 5.5

Write code that executes correctly on WAMP Server 5.2 or lower without requiring any changes.

---

# Database Rules

- Use MySQL with the MySQLi extension.
- Use prepared statements wherever practical.
- Keep queries simple and readable.
- Use proper primary keys and foreign keys.
- Use indexing where appropriate.
- Store images as file paths, not as BLOB data.
- Keep database normalization simple and suitable for a college project.

---

# Project Structure

Always follow the project documentation.

Use:

- STRUCTURE.md
- DATABASE.md
- DESIGN.md

Do not change the folder structure unless explicitly instructed.

---

# Coding Style

- Write clean and beginner-friendly code.
- Keep the code as simple as possible.
- Avoid unnecessary complexity.
- Avoid duplicate code.
- Reuse PHP, HTML, CSS, and JavaScript whenever possible.
- Create reusable functions.
- Create reusable CSS classes.
- Use meaningful variable names.
- Use meaningful function names.
- Use meaningful file names.
- Keep indentation consistent.
- Add comments only where they improve readability.

---

# HTML Guidelines

- Use semantic HTML5 elements whenever possible.
- Keep markup clean and organized.
- Avoid unnecessary wrapper elements.
- Reuse common layouts.
- Use proper labels for form fields.
- Maintain accessibility where practical.

---

# CSS Guidelines

- Keep CSS reusable.
- Avoid duplicate styles.
- Avoid inline CSS.
- Use separate CSS files.

Recommended structure:

- style.css
- admin.css
- responsive.css

Use consistent:

- Colors
- Typography
- Spacing
- Border radius
- Shadows
- Buttons
- Forms
- Cards

Follow DESIGN.md for all visual decisions.

---

# JavaScript Guidelines

Use only Vanilla JavaScript.

Keep scripts lightweight.

Use JavaScript only where necessary.

Examples:

- Form validation
- Image preview
- Delete confirmation
- Quantity update
- Mobile navigation
- Basic UI interactions

Avoid unnecessary animations and effects.

---

# UI & Design Rules

The interface should:

- Look professional
- Look clean
- Feel modern
- Be minimal
- Be consistent

Avoid designs that look AI-generated.

Maintain one consistent design language across every page.

Never randomly change:

- Colors
- Fonts
- Button styles
- Card styles
- Spacing

Every page should feel like part of the same website.

---

# Responsive Design

The project must support:

- Desktop
- Laptop
- Tablet
- Mobile

Navigation should become a mobile menu on smaller screens.

Use responsive CSS instead of creating separate mobile pages.

---

# Security Rules

Although this is a college project, follow basic security practices.

- Validate all user input.
- Escape output using htmlspecialchars().
- Use prepared statements.
- Protect admin pages.
- Protect user pages.
- Validate uploaded images.
- Destroy sessions correctly during logout.
- Never trust client-side validation alone.

---

# Performance Rules

Keep the project lightweight.

Avoid:

- Large CSS files
- Duplicate JavaScript
- Unnecessary database queries
- Excessive nesting
- Heavy animations

Reuse components whenever possible.

---

# Response Rules

When generating code:

- Answer only what is requested.
- Do not generate multiple files unless requested.
- Do not rewrite unchanged files.
- Do not add features that were not requested.
- Do not change project architecture without permission.
- Before generating a major feature or multiple files, ask for confirmation.
- Keep explanations brief unless requested.
- Generate complete, working code for the requested file.

---

# Development Priority

Always prioritize:

1. Compatibility
2. Simplicity
3. Reusability
4. Readability
5. Maintainability
6. Professional Design

Performance optimizations should never reduce compatibility with PHP 5.5 or lower.

---

# Goal

Build a clean, professional, responsive, and reusable Online Camera Rental System that:

- Runs on WAMP Server 5.2 or lower
- Supports PHP 5.5 or lower
- Uses only PHP, MySQL, HTML, CSS, and Vanilla JavaScript
- Has a consistent professional design
- Uses minimal, reusable code
- Is easy for a BCA student to understand and maintain
- Meets college submission requirements while following good programming practices

---

# Project Documentation

Always use the following documents as the primary reference before implementing any feature.

1. DATABASE.md
2. DESIGN.md
3. STRUCTURE.md

If any implementation conflicts with these documents, follow the documentation unless instructed otherwise.