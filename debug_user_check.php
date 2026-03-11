<?php
include __DIR__ . '/config/db.php';

$email = 'student_test@example.com';
$password = 'Test@1234';

$res = mysqli_query($conn, "SELECT id, name, email, role, password FROM users WHERE email='" . mysqli_real_escape_string($conn, $email) . "' LIMIT 1");
if (!$res) {
    echo "QUERY_ERROR: " . mysqli_error($conn) . "\n";
    exit(1);
}

$row = mysqli_fetch_assoc($res);
if (!$row) {
    echo "NOT_FOUND\n";
    exit(1);
}

$check = password_verify($password, $row['password']);
$output = [
    'id' => $row['id'],
    'name' => $row['name'],
    'email' => $row['email'],
    'role' => $row['role'],
    'password_hash' => substr($row['password'],0,60) . '...',
    'password_verify' => $check ? 'MATCH' : 'NO_MATCH'
];

echo json_encode($output, JSON_PRETTY_PRINT) . "\n";
