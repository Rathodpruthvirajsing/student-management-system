<?php
include "c:/xampp/htdocs/student-management-system/config/db.php";
echo "--- Searching for all Admins ---\n";
$query = "SELECT id, email, name, role FROM users WHERE role='admin' OR email LIKE '%admin%'";
$res = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: {$row['id']} | Email: {$row['email']} | Name: {$row['name']} | Role: {$row['role']}\n";
}
?>
