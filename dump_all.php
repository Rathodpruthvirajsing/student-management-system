<?php
include "config/db.php";
echo "--- ALL USERS ---\n";
$q = mysqli_query($conn, "SELECT id, email, role FROM users");
while($r = mysqli_fetch_assoc($q)) {
    echo "ID: {$r['id']} | Email: {$r['email']} | Role: {$r['role']}\n";
}
echo "--- ALL STUDENTS ---\n";
$q = mysqli_query($conn, "SELECT id, email, name FROM students");
while($r = mysqli_fetch_assoc($q)) {
    echo "ID: {$r['id']} | Email: {$r['email']} | Name: {$r['name']}\n";
}
