<?php
include 'config/db.php';
echo "--- USERS ---\n";
$r = mysqli_query($conn, 'SELECT email, role FROM users');
while($u=mysqli_fetch_assoc($r)) echo $u['role'].': '.$u['email']."\n";

echo "\n--- PARENTS ---\n";
$r = mysqli_query($conn, 'SELECT email, name, student_id FROM parents');
while($p=mysqli_fetch_assoc($r)) echo $p['email']." - ".$p['name']." - StudentID: ".$p['student_id']."\n";
?>
