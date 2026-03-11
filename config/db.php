<?php
// Database Configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "student_db";

// Global connection variable
global $conn;

// Check if connection already exists and is valid
if (!isset($conn) || !is_object($conn) || get_class($conn) !== 'mysqli' || !$conn->ping()) {
    // Create new connection
    $conn = mysqli_connect($host, $user, $password, $database);

    // Check connection
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Set charset to utf8
    mysqli_set_charset($conn, "utf8");

    // Set connection timeout
    mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    
    // Enable proper error handling
    mysqli_report(MYSQLI_REPORT_OFF); // Disable automatic error reporting to prevent issues
}

?>