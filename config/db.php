<?php
// Smart Database Configuration
// This file automatically detects if it's running locally (XAMPP) or on a live server (InfinityFree)

// Robust Environment Detection
$is_local = false;
$local_indicators = ['localhost', '127.0.0.1', '192.168.', '::1'];
$current_host = $_SERVER['HTTP_HOST'] ?? '';

foreach ($local_indicators as $indicator) {
    if (stripos($current_host, $indicator) !== false) {
        $is_local = true;
        break;
    }
}

// Fallback: Check if we are in a XAMPP directory
if (!$is_local && stripos(__FILE__, 'xampp') !== false) {
    $is_local = true;
}

if ($is_local) {
    // Local Settings (XAMPP)
    $host = "localhost";
    $user = "root";
    $password = "";
    $database = "student_db";
} else {
    // InfinityFree Settings
    // IMPORTANT: These are placeholders. You must replace them with details from your Control Panel
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
    // Use @ to suppress the warning as we handle errors manually
    $conn = @mysqli_connect($host, $user, $password, $database);

    // Check connection
    if (!$conn) {
        // If live connection fails and we thought it was live, maybe show a better message
        if (!$is_local) {
            die("Live Database connection failed. Please check your credentials in config/db.php. Error: " . mysqli_connect_error());
        }
        die("Local Database connection failed: " . mysqli_connect_error());
    }

    // Set charset to utf8
    mysqli_set_charset($conn, "utf8");

    // Set connection timeout
    mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    
    // Disable automatic error reporting to prevent issues on production
    mysqli_report(MYSQLI_REPORT_OFF);
}
?>
