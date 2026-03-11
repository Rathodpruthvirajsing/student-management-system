<?php
include __DIR__ . '/config/db.php';

$sql = "UPDATE users u JOIN students s ON u.email = s.email SET u.role = 'student' WHERE (u.role = '' OR u.role IS NULL);";
$res = mysqli_query($conn, $sql);
if ($res === false) {
    echo "UPDATE_ERROR: " . mysqli_error($conn) . "\n";
    exit(1);
}

// Show affected rows
echo "Updated rows: " . mysqli_affected_rows($conn) . "\n";

// Show the test user row
$email = 'student_test@example.com';
$r = mysqli_query($conn, "SELECT id, name, email, role FROM users WHERE email='" . mysqli_real_escape_string($conn, $email) . "' LIMIT 1");
$row = mysqli_fetch_assoc($r);
if ($row) echo json_encode($row) . "\n";
else echo "Test user not found\n";
