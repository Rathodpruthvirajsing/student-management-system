<?php
include "c:/xampp/htdocs/student-management-system/config/db.php";
echo "--- Admin Users List ---\n";
$query = "SELECT id, name, email FROM users WHERE role='admin'";
$res = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: {$row['id']} | Email: {$row['email']} | Name: {$row['name']}\n";
}
?>
