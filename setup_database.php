<?php
// Database setup script
$host = 'localhost';
$user = 'root';
$password = '070817';
$dbname = 'little_lemon';

// Create connection without specifying database first
$conn = mysqli_connect($host, $user, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS little_lemon";
if (!mysqli_query($conn, $sql)) {
    die("Error creating database: " . mysqli_error($conn));
}

// Select the database
mysqli_select_db($conn, $dbname);

// SQL to create tables
$tables = array(
    // USERS TABLE
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // CATEGORIES TABLE
    "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    // MENU TABLE
    "CREATE TABLE IF NOT EXISTS menu (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        price DECIMAL(6,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    )",
    
    // RESERVATIONS TABLE
    "CREATE TABLE IF NOT EXISTS reservations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        reserve_date DATE NOT NULL,
        reserve_time TIME NOT NULL,
        guests INT NOT NULL,
        status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    // ORDERS TABLE
    "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        order_type ENUM('dine-in','takeaway') NOT NULL,
        reservation_id INT NULL,
        total DECIMAL(8,2) NOT NULL,
        status ENUM('pending','completed','cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL
    )",
    
    // ORDER ITEMS TABLE
    "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        menu_id INT NOT NULL,
        quantity INT NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
    )"
);

// Execute all table creation queries
$success = true;
foreach ($tables as $sql) {
    if (!mysqli_query($conn, $sql)) {
        echo "Error creating table: " . mysqli_error($conn) . "<br>";
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

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <!-- Output from PHP above -->
    </div>
</body>
</html>
