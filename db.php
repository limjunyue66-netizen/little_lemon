<?php
session_start();

// Database connection settings
$host = 'localhost';
$user = 'root';
$password = '070817';
$dbname = 'little_lemon';

// Create connection
$conn = mysqli_connect($host, $user, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, "utf8");

// Check if user is logged in
if(!isset($_SESSION['user']) && !in_array(basename($_SERVER['PHP_SELF']), ['register.php', 'login.php', 'index.php'])) {
    // Allow access to certain pages without login
    if(!in_array(basename($_SERVER['PHP_SELF']), ['user_menu.php'])) {
        // header("Location: login.php");
    }
}
?>
