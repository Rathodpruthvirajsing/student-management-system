<?php
include __DIR__ . '/config/db.php';

// Alter users.role enum to include 'student'
$alter = "ALTER TABLE users MODIFY role ENUM('admin','teacher','student') DEFAULT 'admin'";
if (!mysqli_query($conn, $alter)) {
    echo "ALTER_ERROR: " . mysqli_error($conn) . "\n";
    exit(1);
}

// Now set student roles where matching student exists
$update = "UPDATE users u JOIN students s ON u.email = s.email SET u.role = 'student' WHERE (u.role = '' OR u.role IS NULL OR u.role != 'student')";
if (!mysqli_query($conn, $update)) {
    echo "UPDATE_ERROR: " . mysqli_error($conn) . "\n";
    exit(1);
}

echo "Alter and update completed. Affected rows: " . mysqli_affected_rows($conn) . "\n";

// Show test user
$email = 'student_test@example.com';
$r = mysqli_query($conn, "SELECT id, name, email, role FROM users WHERE email='" . mysqli_real_escape_string($conn, $email) . "' LIMIT 1");
$row = mysqli_fetch_assoc($r);
if ($row) echo json_encode($row) . "\n";
else echo "Test user not found\n";
