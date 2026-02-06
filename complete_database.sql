-- Little Lemon Restaurant Management System
-- Complete Database Schema

CREATE DATABASE IF NOT EXISTS little_lemon;
USE little_lemon;

-- USERS TABLE
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CATEGORIES TABLE
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- MENU TABLE
CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100),
    price DECIMAL(6,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- RESERVATIONS TABLE
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    reserve_date DATE,
    reserve_time TIME,
    guests INT,
    table_number VARCHAR(10) DEFAULT NULL,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ORDERS TABLE
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_type ENUM('dine-in','takeaway'),
    reservation_id INT NULL,
    total DECIMAL(8,2),
    status ENUM('pending','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL
);

-- ORDER ITEMS TABLE
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    menu_id INT,
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
);

-- Insert Default Categories
INSERT INTO categories (name) VALUES 
('Appetizers'),
('Main Course'),
('Desserts'),
('Beverages');

-- Insert Sample Menu Items
INSERT INTO menu (category_id, name, price) VALUES 
(1, 'Garlic Bread', 4.99),
(1, 'Bruschetta', 6.99),
(2, 'Grilled Salmon', 18.99),
(2, 'Chicken Parmesan', 15.99),
(2, 'Vegetable Pasta', 12.99),
(3, 'Tiramisu', 7.99),
(3, 'Cheesecake', 8.99),
(4, 'Iced Tea', 2.99),
(4, 'Fresh Orange Juice', 3.99);

-- Create Admin User (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin', 'admin@littlelemon.com', '$2y$10$Y5F0qXvIl0Pz1zKx0Z9q7eZ7Y8X7W6V5U4T3S2R1Q0P9O8N7M6L5', 'admin');
