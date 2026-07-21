---

# Technical Compatibility

This project is designed for educational purposes and must remain compatible with older PHP environments.

## Target Environment

- WAMP Server 5.2 or lower
- PHP 5.5 or lower
- Apache
- MySQL 5.x
- MySQLi Extension

The project must not require Composer, frameworks, or any PHP feature introduced after PHP 5.5.

---
STRUCTURE.md
Online Camera Rental System
Version: 1.0

Project Structure
The project follows a simple and modular folder structure that is fully compatible with PHP 5.x, MySQL, and WAMP 5.2.
The project is divided into separate modules for the User Panel, Admin Panel, Shared Includes, Assets, and Database.
camera-rental-system/
│
├── admin/
├── user/
├── includes/
├── assets/
├── database/
│
├── index.php
├── login.php
├── signup.php
├── logout.php
│
└── README.md

Root Directory
index.php
Public homepage of the website.
Displays:

Hero Section
Featured Cameras
Categories
About Preview
Contact Preview


login.php
Common login page for both User and Admin.
After successful login:

User → User Home
Admin → Admin Dashboard


signup.php
New user registration page.
Only customers can register.
Admin accounts are created directly in the database.

logout.php
Destroys the session and redirects the user to the homepage.

Admin Module
Location
admin/
Contains all pages related to the administrator.
admin/
│
├── dashboard.php
├── manage-cameras.php
├── add-camera.php
├── edit-camera.php
├── delete-camera.php
│
├── manage-brands.php
├── add-brand.php
├── edit-brand.php
├── delete-brand.php
│
├── manage-customers.php
├── customer-details.php
│
├── manage-bookings.php
├── booking-details.php
├── update-booking-status.php
│
├── rent-status.php
└── logout.php

dashboard.php
Displays

Total Cameras
Total Customers
Total Bookings
Active Rentals
Available Cameras


Camera Management
manage-cameras.php
Displays all cameras.
Supports:

Search
Edit
Delete


add-camera.php
Add new camera.
Fields

Camera Name
Brand
Category
Price Per Day
Quantity
Description
Image


edit-camera.php
Update existing camera information.

delete-camera.php
Delete camera record.

Brand Management
manage-brands.php
Displays all camera brands.

add-brand.php
Create new brand.

edit-brand.php
Update brand name.

delete-brand.php
Remove brand.

Customer Management
manage-customers.php
Displays all registered customers.

customer-details.php
Displays complete customer information.

Booking Management
manage-bookings.php
Displays all bookings.
Supports:

View Details
Update Status


booking-details.php
Displays

Customer
Camera
Dates
Total Amount
Status


update-booking-status.php
Update booking status.
Available Status

Pending
Approved
Picked Up
Returned
Cancelled


rent-status.php
Displays

Active Rentals
Returned Rentals
Cancelled Rentals


logout.php
Logs out administrator.

User Module
Location
user/
Contains all customer pages.
user/
│
├── home.php
├── browse-cameras.php
├── camera-details.php
├── cart.php
├── booking-confirmation.php
├── about.php
└── contact.php

home.php
Customer homepage.
Contains

Hero
Featured Cameras
Categories
Why Choose Us


browse-cameras.php
Displays all available cameras.
Supports

Search
Filter
Category
Brand


camera-details.php
Displays

Camera Image
Camera Information
Specifications
Rental Price
Availability
Rent Button


cart.php
Shopping cart.
Supports

Add Camera
Remove Camera
Update Quantity
Calculate Total


booking-confirmation.php
Displays

Booking Success
Booking History
Booking Status

Shows

Booking ID
Pickup Date
Return Date
Total Amount
Status


about.php
Displays project information.

contact.php
Contact form.
Fields

Name
Email
Subject
Message


Shared Includes
Location
includes/
Contains reusable files used throughout the project.
includes/
│
├── config.php
├── database.php
├── session.php
├── functions.php
├── admin-auth.php
├── user-auth.php
│
├── header.php
├── footer.php
├── navbar.php
├── admin-sidebar.php
└── admin-header.php

config.php
Stores project configuration values.
Example

Project Name
Default Settings


database.php
Creates the MySQL database connection.
Used by every page requiring database access.

session.php
Starts PHP session.

functions.php
Contains reusable PHP functions.
Examples

Generate Booking ID
Calculate Rental Days
Calculate Total Price
Upload Image
Validate Input


admin-auth.php
Protects all admin pages.
Redirects unauthorized users.

user-auth.php
Protects all customer pages.
Redirects unauthorized users.

header.php
Common HTML header.
Contains

DOCTYPE
Meta Tags
CSS Links


navbar.php
Common navigation bar for user pages.

footer.php
Common website footer.

admin-sidebar.php
Sidebar navigation for administrator.

admin-header.php
Top navigation for administrator.

Assets
Location
assets/
Contains all static resources.
assets/
│
├── css/
├── js/
├── images/
└── uploads/

CSS
assets/css/
│
├── style.css
├── admin.css
└── responsive.css
style.css
Main stylesheet for the user interface.

admin.css
Styles for the administrator dashboard.

responsive.css
Responsive layouts for tablets and mobile devices.

JavaScript
assets/js/
script.js
Contains

Form Validation
Menu Toggle
Image Preview
Delete Confirmation
Quantity Update
Basic UI Interactions

Only Vanilla JavaScript is used.

Images
assets/images/
│
├── logo/
├── hero/
├── cameras/
├── brands/
├── icons/
└── banners/
logo/
Project logo.

hero/
Homepage banner images.

cameras/
Default camera images.

brands/
Brand logos.

icons/
Project icons.

banners/
Additional promotional banners.

Uploads
assets/uploads/
│
└── cameras/
Stores camera images uploaded by the administrator.
Uploaded files are separated from project assets.

Database
Location
database/
Contains
camera_rental.sql
The SQL file includes

Database Creation
Tables
Relationships
Sample Admin Account
Sample Brands
Sample Cameras


Authentication Flow
Home

↓

Login

↓

Check Email & Password

↓

Check Role

↓

Admin
    ↓
Dashboard

User
    ↓
Home

Folder Responsibility
FolderResponsibilityadminAdministrator PaneluserCustomer PagesincludesShared reusable PHP filesassetsCSS, JavaScript, Images, UploadsdatabaseSQL DatabaserootPublic pages

Development Guidelines

Use only PHP 5.x compatible syntax.
Use MySQL with the MySQLi extension.
Do not use any PHP frameworks.
Do not use Composer or external libraries.
Use only HTML, CSS, JavaScript, PHP, and MySQL.
Keep CSS reusable and avoid duplicate code.
Reuse shared components from the includes folder.
Maintain consistent file naming using lowercase letters and hyphens.
Write clean, readable, and well-commented code suitable for a college project.