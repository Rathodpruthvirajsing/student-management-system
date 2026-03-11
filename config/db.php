<?php
// Smart Database Configuration
// This file automatically detects if it's running locally (XAMPP) or on a live server (InfinityFree)

if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // Local Settings (XAMPP)
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "student_db";
} else {
    // InfinityFree Settings
    // IMPORTANT: Login to your InfinityFree control panel to get these details
    // Go to "MySQL Databases" to find your Host, Username, and Password
    $host = "sqlXXX.epizy.com";      // Replace with your MySQL Hostname
    $user = "epiz_XXXX_XXXX";        // Replace with your MySQL Username
    $password = "Your_FTP_Password"; // Replace with your MySQL Password
    $database = "epiz_XXXX_XXXX_db"; // Replace with your MySQL Database Name
}

// Global connection variable
global $conn;

// Check if connection already exists and is valid
if (!isset($conn) || !is_object($conn) || get_class($conn) !== 'mysqli' || !$conn->ping()) {
    // Create new connection
    $conn = mysqli_connect($host, $user, $password, $database);

    // Check connection
    if (!$conn) {
        die(" Database connection failed: " . mysqli_connect_error());
    }

    // Set charset to utf8
    mysqli_set_charset($conn, "utf8");

    // Set connection timeout
    mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    
    // Disable automatic error reporting to prevent issues on production
    mysqli_report(MYSQLI_REPORT_OFF);
}
?>