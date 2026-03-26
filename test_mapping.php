<?php
include "config/db.php";
$q = mysqli_query($conn, "SELECT id, email FROM users WHERE role='student'");
echo "STUDENT USERS:\n";
while($r = mysqli_fetch_assoc($q)) { print_r($r); }

$q2 = mysqli_query($conn, "SELECT id, email, course_id FROM students");
echo "STUDENT RECORDS:\n";
while($r = mysqli_fetch_assoc($q2)) { print_r($r); }
?>
