<?php
// Database setup script
$host = 'localhost';
$user = 'root';
$password = '';  // Updated for XAMPP default
$dbname = 'little_lemon';

// Create connection without specifying database first
$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS little_lemon";
if (!$conn->query($sql)) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// SQL to create tables
$tables = array(
    // USERS TABLE
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(100) UNIQUE,
        password VARCHAR(255),
        role ENUM('admin','user') DEFAULT 'user'
    )",
    
    // CATEGORIES TABLE
    "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50)
    )",
    
    // MENU TABLE
    "CREATE TABLE IF NOT EXISTS menu (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT,
        name VARCHAR(100),
        price DECIMAL(6,2),
        image_url VARCHAR(500) DEFAULT NULL,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    )",
    
    // RESERVATIONS TABLE
    "CREATE TABLE IF NOT EXISTS reservations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        reserve_date DATE,
        reserve_time TIME,
        guests INT,
        table_number VARCHAR(10) DEFAULT NULL,
        status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // ORDERS TABLE
    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        order_type ENUM('dine-in','takeaway'),
        reservation_id INT NULL,
        total DECIMAL(8,2),
        status ENUM('pending','completed','cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL
    )",
    
    // ORDER ITEMS TABLE
    "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        menu_id INT,
        quantity INT,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
    )"
);

// Execute all table creation queries
$success = true;
foreach ($tables as $sql) {
    if (!$conn->query($sql)) {
        echo "Error creating table: " . $conn->error . "<br>";
        $success = false;
    }
}

if ($success) {
    echo "<div class='alert alert-success' style='padding: 20px; margin: 20px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<h4>✓ Database Setup Complete!</h4>";
    echo "<p>All tables have been created successfully.</p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Delete this setup file (setup_database.php)</li>";
    echo "<li>Visit <a href='index.php'>index.php</a> to start using the application</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div class='alert alert-danger' style='padding: 20px; margin: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h4>✗ Error Setting Up Database</h4>";
    echo "<p>Please check the errors above.</p>";
    echo "</div>";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/responsive.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <!-- Output from PHP above -->
    </div>
</body>
</html>
