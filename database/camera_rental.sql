-- =========================================
-- Camera Rental System - Database
-- =========================================

CREATE DATABASE IF NOT EXISTS camera_rental CHARACTER SET utf8 COLLATE utf8_general_ci;
USE camera_rental;

-- ===== 1. Users =====
CREATE TABLE tbl_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ===== 2. Brands =====
CREATE TABLE tbl_brands (
    brand_id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(brand_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ===== 3. Categories =====
CREATE TABLE tbl_categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(category_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ===== 4. Cameras =====
CREATE TABLE tbl_cameras (
    camera_id INT AUTO_INCREMENT PRIMARY KEY,
    brand_id INT NOT NULL,
    category_id INT NOT NULL,
    camera_name VARCHAR(150) NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    description TEXT,
    image VARCHAR(255),
    status ENUM('Available','Booked','Maintenance','Unavailable') NOT NULL DEFAULT 'Available',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX(brand_id),
    INDEX(category_id),
    INDEX(status),
    FOREIGN KEY (brand_id) REFERENCES tbl_brands(brand_id),
    FOREIGN KEY (category_id) REFERENCES tbl_categories(category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ===== 5. Cart =====
CREATE TABLE tbl_cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    camera_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX(user_id),
    INDEX(camera_id),
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id),
    FOREIGN KEY (camera_id) REFERENCES tbl_cameras(camera_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ===== 6. Bookings =====
CREATE TABLE tbl_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    booking_number VARCHAR(50) NOT NULL,
    pickup_date DATE NOT NULL,
    return_date DATE NOT NULL,
    total_days INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_status ENUM('Pending','Approved','Picked Up','Returned','Cancelled') NOT NULL DEFAULT 'Pending',
    booking_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX(user_id),
    INDEX(booking_status),
    INDEX(booking_date),
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ===== 7. Booking Items =====
CREATE TABLE tbl_booking_items (
    booking_item_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    camera_id INT NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    subtotal DECIMAL(10,2) NOT NULL,
    INDEX(booking_id),
    INDEX(camera_id),
    FOREIGN KEY (booking_id) REFERENCES tbl_bookings(booking_id),
    FOREIGN KEY (camera_id) REFERENCES tbl_cameras(camera_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ===== 8. Contact =====
CREATE TABLE tbl_contact (
    contact_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- =========================================
-- Sample Data
-- =========================================

-- Default Admin (email: admin@gmail.com / password: admin123)
INSERT INTO tbl_users (full_name, email, phone, password, role) VALUES
('Admin', 'admin@gmail.com', '9999999999', '$2y$10$jr1s1Ual9zOo9kBjqCU0HuNAKcB1NcgRNxP2SKX322XCSdE4wwb7e', 'admin');

-- Sample Brands
INSERT INTO tbl_brands (brand_name) VALUES
('Canon'), ('Sony'), ('Nikon'), ('Fujifilm');

-- Sample Categories
INSERT INTO tbl_categories (category_name) VALUES
('DSLR'), ('Mirrorless'), ('Action Camera'), ('Lens');

-- Sample Cameras
INSERT INTO tbl_cameras (brand_id, category_id, camera_name, price_per_day, quantity, description, image, status) VALUES
(1, 1, 'Canon EOS 1500D', 500.00, 5, 'Entry level DSLR camera with 24MP sensor.', 'canon-1500d.jpg', 'Available'),
(2, 2, 'Sony Alpha A7 III', 1500.00, 3, 'Full-frame mirrorless camera for professionals.', 'sony-a7iii.jpg', 'Available'),
(3, 1, 'Nikon D3500', 450.00, 4, 'Lightweight DSLR ideal for beginners.', 'nikon-d3500.jpg', 'Available'),
(4, 2, 'Fujifilm X-T4', 1300.00, 2, 'Mirrorless camera with excellent color science.', 'fujifilm-xt4.jpg', 'Available');