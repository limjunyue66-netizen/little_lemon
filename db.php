<?php
session_start();

// Database connection settings
$host = 'localhost';
$user = 'root';
$password = '070817';  // Updated for XAMPP default
$dbname = 'little_lemon';

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Contact phone number for direct bookings (change as needed)
$contact_phone = '+1-555-123-4567';

// Check if user is logged in
if(!isset($_SESSION['user']) && !in_array(basename($_SERVER['PHP_SELF']), ['register.php', 'login.php', 'index.php'])) {
    // Allow access to certain pages without login
    if(!in_array(basename($_SERVER['PHP_SELF']), ['user_menu.php'])) {
        // header("Location: login.php");
    }
}
?>