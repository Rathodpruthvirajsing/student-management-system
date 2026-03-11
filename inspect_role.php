<?php
include __DIR__ . '/config/db.php';
$email = 'student_test@example.com';
$r = mysqli_query($conn, "SELECT id, email, role, CHAR_LENGTH(role) as len, HEX(role) as hex FROM users WHERE email='" . mysqli_real_escape_string($conn, $email) . "' LIMIT 1");
$row = mysqli_fetch_assoc($r);
if (!$row) { echo "NOT_FOUND\n"; exit; }
echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
