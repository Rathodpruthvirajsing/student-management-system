<?php
include "c:/xampp/htdocs/student-management-system/config/db.php";

echo "--- Searching for 'utsav' in 'users' table ---\n";
$query = "SELECT id, name, email, role, password FROM users WHERE name LIKE '%utsav%' OR email LIKE '%utsav%'";
$res = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($res)) {
    echo "User ID: {$row['id']} | Name: {$row['name']} | Email: {$row['email']} | Role: {$row['role']} | Pass Hash: " . substr($row['password'], 0, 10) . "...\n";
}

echo "\n--- Searching for 'utsav' in 'students' table ---\n";
$query = "SELECT id, name, email, enrollment_no FROM students WHERE name LIKE '%utsav%' OR email LIKE '%utsav%'";
$res = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($res)) {
    echo "Student ID: {$row['id']} | Name: {$row['name']} | Email: {$row['email']} | Enrollment: {$row['enrollment_no']}\n";
}
?>
