<?php
include __DIR__ . '/config/db.php';
$email = 'student_test@example.com';
$q = "SELECT u.id as uid, u.email as u_email, u.role as u_role, s.id as sid, s.email as s_email FROM users u LEFT JOIN students s ON u.email = s.email WHERE u.email='" . mysqli_real_escape_string($conn, $email) . "'";
$res = mysqli_query($conn, $q);
if (!$res) { echo "ERROR: " . mysqli_error($conn) . "\n"; exit; }
while($r = mysqli_fetch_assoc($res)) echo json_encode($r, JSON_PRETTY_PRINT) . "\n";
