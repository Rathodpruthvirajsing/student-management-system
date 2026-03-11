<?php
include "c:/xampp/htdocs/student-management-system/config/db.php";

echo "--- User Details for ID 11 ---\n";
$query = "SELECT id, name, email, role FROM users WHERE id=11";
$res = mysqli_query($conn, $query);
if($row = mysqli_fetch_assoc($res)) {
    echo "User ID: {$row['id']} | Name: {$row['name']} | Email: {$row['email']} | Role: '{$row['role']}'\n";
} else {
    echo "User 11 not found.\n";
}

echo "\n--- Recent Users (Last 5) ---\n";
$query = "SELECT id, name, email, role FROM users ORDER BY id DESC LIMIT 5";
$res = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($res)) {
    echo "User ID: {$row['id']} | Name: {$row['name']} | Email: {$row['email']} | Role: '{$row['role']}'\n";
}
?>
