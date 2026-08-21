DATABASE.md
Online Camera Rental System
Version: 1.0

1. Overview
Database Name
camera_rental
Database Type
MySQL
Compatibility

PHP 5.x
MySQL 5.x
WAMP 5.2
MySQLi Extension


2. Database Objectives
The database is designed to:

Store user accounts
Store administrator accounts
Store camera brands
Store camera categories
Store camera information
Store shopping cart data
Store bookings
Store booking items
Store contact form submissions

The database follows normalization principles to reduce data redundancy and improve maintainability.

3. Database Naming Convention
Database
camera_rental
Tables
All tables use the prefix:
tbl_
Example
tbl_users
tbl_cameras
tbl_bookings

Primary Keys
Every table uses
id
Example
user_id
camera_id
booking_id

Foreign Keys
Foreign keys always use the referenced table name.
Examples
brand_id
camera_id
user_id
booking_id

File Naming
Lowercase
Hyphen separated
Example
manage-cameras.php
camera-details.php

4. Character Set
Character Set
utf8
Collation
utf8_general_ci

5. Database Tables
Total Tables
8

tbl_users
tbl_brands
tbl_categories
tbl_cameras
tbl_cart
tbl_bookings
tbl_booking_items
tbl_contact


6. Table Details
6.1 tbl_users
Purpose
Stores both customer and administrator accounts.
Columns
ColumnTypeLengthNullDefaultDescriptionuser_idINT-NOAUTO_INCREMENTPrimary Keyfull_nameVARCHAR100NO-User NameemailVARCHAR100NO-Login EmailphoneVARCHAR15NO-Mobile NumberpasswordVARCHAR255NO-Encrypted PasswordroleENUM-NOuseradmin / usercreated_atDATETIME-NOCURRENT_TIMESTAMPRegistration Date
Primary Key
user_id
Indexes
PRIMARY
UNIQUE(email)

6.2 tbl_brands
Purpose
Stores camera brands.
Columns
brand_id
brand_name
created_at
Indexes
PRIMARY
UNIQUE(brand_name)

6.3 tbl_categories
Purpose
Stores camera categories.
Examples
DSLR
Mirrorless
Action Camera
Drone
Lens
Columns
category_id
category_name
created_at
Indexes
PRIMARY
UNIQUE(category_name)

6.4 tbl_cameras
Purpose
Stores all rentable cameras.
Columns
camera_id
brand_id
category_id
camera_name
price_per_day
quantity
description
image
status
created_at
Status Values
Available
Booked
Maintenance
Unavailable
Indexes
PRIMARY
INDEX(brand_id)
INDEX(category_id)
INDEX(status)

6.5 tbl_cart
Purpose
Stores temporary cart items.
Columns
cart_id
user_id
camera_id
quantity
created_at
Indexes
PRIMARY
INDEX(user_id)
INDEX(camera_id)

6.6 tbl_bookings
Purpose
Stores booking information.
Columns
booking_id
user_id
phone_number
booking_number
pickup_date
return_date
total_days
total_amount
booking_status
booking_date
Booking Status
Pending
Approved
Picked Up
Returned
Cancelled
Indexes
PRIMARY
INDEX(user_id)
INDEX(booking_status)
INDEX(booking_date)

6.7 tbl_booking_items
Purpose
Stores cameras inside each booking.
Columns
booking_item_id
booking_id
camera_id
price_per_day
quantity
subtotal
Indexes
PRIMARY
INDEX(booking_id)
INDEX(camera_id)

6.8 tbl_contact
Purpose
Stores contact form submissions.
Columns
contact_id
name
email
subject
message
created_at
Indexes
PRIMARY
INDEX(email)

7. Relationships
tbl_users
↓
tbl_bookings
↓
tbl_booking_items
↓
tbl_cameras
↓
tbl_brands
↓
tbl_categories

8. Relationship Details
One User
↓
Many Bookings
One Booking
↓
Many Booking Items
One Brand
↓
Many Cameras
One Category
↓
Many Cameras
One Camera
↓
Many Booking Items
One User
↓
Many Cart Items

9. Indexing Strategy
Indexes improve search performance.
tbl_users
PRIMARY(user_id)
UNIQUE(email)
tbl_brands
PRIMARY(brand_id)
UNIQUE(brand_name)
tbl_categories
PRIMARY(category_id)
UNIQUE(category_name)
tbl_cameras
PRIMARY(camera_id)
INDEX(brand_id)
INDEX(category_id)
INDEX(status)
tbl_cart
PRIMARY(cart_id)
INDEX(user_id)
INDEX(camera_id)
tbl_bookings
PRIMARY(booking_id)
INDEX(user_id)
INDEX(booking_status)
INDEX(booking_date)
tbl_booking_items
PRIMARY(booking_item_id)
INDEX(booking_id)
INDEX(camera_id)
tbl_contact
PRIMARY(contact_id)
INDEX(email)

10. Constraints
Every table has a Primary Key.
Email addresses must be unique.
Brand names must be unique.
Category names must be unique.
Quantity cannot be negative.
Price must be greater than zero.
Pickup Date cannot be greater than Return Date.

11. Default Admin Account
Email
admin@gmail.com
Password
admin123
Role
admin

12. Booking Flow
User
↓
Browse Camera
↓
Add to Cart
↓
Checkout
↓
Booking Created
↓
Booking Items Saved
↓
Admin Approval
↓
Return Camera
↓
Booking Completed

13. Camera Availability Flow
Available
↓
Booked
↓
Picked Up
↓
Returned
↓
Available
or
Available
↓
Maintenance
↓
Available

14. Data Validation Rules
Email must be unique.
Phone number should contain only digits.
Rental days must be at least one day.
Price cannot be negative.
Image uploads should be JPG, JPEG, or PNG.

15. Database Normalization
The database follows the First, Second, and Third Normal Forms (1NF, 2NF, and 3NF).
This minimizes data redundancy by separating brands, categories, bookings, and booking items into individual tables.

16. Security Considerations

Store passwords using password hashing (or the strongest approach compatible with the PHP version being used).
Validate all user inputs.
Escape data before database queries.
Restrict administrator pages using role-based authentication.
Never trust client-side validation alone.


17. Future Enhancements
Possible future improvements include:

Online payment gateway
Camera accessories rental
Wishlist
User reviews and ratings
Invoice generation
Discount coupons
Email notifications
SMS notifications
Inventory reports
Sales reports


18. Database Summary
Database Name
camera_rental
Total Tables
8
Primary Keys
8
Foreign Key Relationships
Logical relationships between Users, Brands, Categories, Cameras, Bookings, Booking Items, Cart, and Contact.
Database Type
Relational Database (MySQL)
Compatibility
PHP 5.x
MySQL
WAMP 5.2