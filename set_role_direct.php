<?php
include __DIR__ . '/config/db.php';
$email = 'student_test@example.com';
$sql = "UPDATE users SET role='student' WHERE email='" . mysqli_real_escape_string($conn,$email) . "'";
$res = mysqli_query($conn, $sql);
if ($res === false) { echo "ERROR: " . mysqli_error($conn) . "\n"; exit(1); }
echo "Affected: " . mysqli_affected_rows($conn) . "\n";
$r = mysqli_query($conn, "SELECT id,email,role,HEX(role) as hex,CHAR_LENGTH(role) as len FROM users WHERE email='" . mysqli_real_escape_string($conn,$email) . "'");
print_r(mysqli_fetch_assoc($r));
