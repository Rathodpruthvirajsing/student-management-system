<?php
// create_test_student.php
// Run in browser to create a test user + matching student row for end-to-end testing.

include "config/db.php";

$testEmail = 'student_test@example.com';
$testName = 'Test Student';
$testPassword = 'Test@1234';
$enrollment = 'TEST001';
$course_id = 1; // adjust if you have different course ids

// Check users table
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE email='" . mysqli_real_escape_string($conn, $testEmail) . "'"));
if (!$user) {
    $hashed = password_hash($testPassword, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
    mysqli_stmt_bind_param($stmt, 'sss', $testName, $testEmail, $hashed);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "Created user: $testEmail<br>";
} else {
    echo "User already exists: $testEmail<br>";
}

// Ensure students table has matching row
$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM students WHERE email='" . mysqli_real_escape_string($conn, $testEmail) . "'"));
if (!$student) {
    $stmt2 = mysqli_prepare($conn, "INSERT INTO students (name, email, enrollment_no, course_id) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt2, 'sssi', $testName, $testEmail, $enrollment, $course_id);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);
    echo "Created student record for: $testEmail<br>";
} else {
    echo "Student record already exists for: $testEmail<br>";
}

echo "<hr>Test credentials:\nEmail: $testEmail<br>Password: $testPassword<br>";

echo "<p>Now: 1) Clear browser cookies or open a private window. 2) Go to <a href=\"index.php\">Login</a> and sign in as the test student.</p>";
?>