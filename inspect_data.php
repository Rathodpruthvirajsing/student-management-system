<?php
include "config/db.php";

echo "--- Mismatched Students (In users but not in students) ---\n";
$query = "SELECT u.id, u.name, u.email FROM users u 
          LEFT JOIN students s ON u.email = s.email 
          WHERE u.role = 'student' AND s.email IS NULL";
$res = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($res)) {
    echo "User ID: {$row['id']} | Name: {$row['name']} | Email: {$row['email']}\n";
}

echo "\n--- Mismatched Students (In students but not in users) ---\n";
$query = "SELECT s.id, s.name, s.email FROM students s 
          LEFT JOIN users u ON s.email = u.email 
          WHERE u.email IS NULL";
$res = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($res)) {
    echo "Student ID: {$row['id']} | Name: {$row['name']} | Email: {$row['email']}\n";
}
