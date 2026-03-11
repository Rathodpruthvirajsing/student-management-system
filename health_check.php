<?php
include "config/db.php";

$out = "--- Student Login Health Check ---\n";

$res = mysqli_query($conn, "SELECT id, name, email, role FROM users WHERE role='student'");
while($user = mysqli_fetch_assoc($res)) {
    $out .= "Checking User: {$user['name']} ({$user['email']})\n";
    $email = mysqli_real_escape_string($conn, $user['email']);
    $student_res = mysqli_query($conn, "SELECT name FROM students WHERE email='$email'");
    if (mysqli_num_rows($student_res) > 0) {
        $student = mysqli_fetch_assoc($student_res);
        $out .= "  - ✓ Found in students table: {$student['name']}\n";
    } else {
        $out .= "  - ✗ MISSING in students table! LOGIN WILL FAIL.\n";
    }
}
file_put_contents("health_check_results.txt", $out);
echo "Results written to health_check_results.txt\n";
