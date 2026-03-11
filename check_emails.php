<?php
include "config/db.php";
$emails = ['utsav12@gmail.com', 'pruthvirathod467@gmail.com', 'pruthvirathod468@gmail.com'];
foreach($emails as $email) {
    echo "Checking '$email':\n";
    $u = mysqli_query($conn, "SELECT role FROM users WHERE email='$email'");
    $s = mysqli_query($conn, "SELECT name FROM students WHERE email='$email'");
    echo "  Users table: " . (mysqli_num_rows($u) > 0 ? "YES (role: " . mysqli_fetch_assoc($u)['role'] . ")" : "NO") . "\n";
    echo "  Students table: " . (mysqli_num_rows($s) > 0 ? "YES (name: " . mysqli_fetch_assoc($s)['name'] . ")" : "NO") . "\n";
}
