<?php
include 'config/db.php';
$roles = ['admin', 'teacher', 'student', 'parent'];
foreach($roles as $role) {
    $r = mysqli_query($conn, "SELECT email FROM users WHERE role='$role' LIMIT 1");
    $u = mysqli_fetch_assoc($r);
    echo strtoupper($role) . "| " . ($u['email'] ?? 'NONE') . "\n";
}
?>
