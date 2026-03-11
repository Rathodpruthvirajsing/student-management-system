<?php
// This script helps diagnose student login issues

echo "<h1>Student Login Diagnostics</h1>";
echo "<style>body { font-family: Arial; background: #f5f5f5; padding: 20px; }</style>";

include "config/db.php";
session_start();

echo "<h2>1. Session Status</h2>";
echo "<pre>";
echo "Session User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "Session Role: " . ($_SESSION['role'] ?? 'NOT SET') . "\n";
echo "Session Name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "\n";
echo "</pre>";

echo "<h2>2. Test Student Email</h2>";
$test_email = "student@example.com"; // Change this to test with different student
$test_query = "SELECT * FROM users WHERE email='$test_email'";
$test_result = mysqli_query($conn, $test_query);

if ($test_result && mysqli_num_rows($test_result) > 0) {
    $user = mysqli_fetch_assoc($test_result);
    echo "<pre>";
    echo "User Found:\n";
    print_r($user);
    echo "</pre>";
    
    // Now check student record
    $student_query = "SELECT * FROM students WHERE email='$test_email'";
    $student_result = mysqli_query($conn, $student_query);
    
    if ($student_result && mysqli_num_rows($student_result) > 0) {
        $student = mysqli_fetch_assoc($student_result);
        echo "<h3>Student Record Found:</h3>";
        echo "<pre>";
        print_r($student);
        echo "</pre>";
    } else {
        echo "<h3 style='color:red;'>ERROR: Student record NOT found for this user!</h3>";
        echo "This is likely the cause of the redirect loop.";
    }
} else {
    echo "<p style='color:red;'>No user found with email: $test_email</p>";
}

echo "<h2>3. All Users in Database</h2>";
$all_users = mysqli_query($conn, "SELECT id, email, name, role FROM users");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Email</th><th>Name</th><th>Role</th></tr>";
while ($row = mysqli_fetch_assoc($all_users)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['role'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>4. All Students in Database</h2>";
$all_students = mysqli_query($conn, "SELECT id, email, name, course_id FROM students");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Email</th><th>Name</th><th>Course ID</th></tr>";
while ($row = mysqli_fetch_assoc($all_students)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['course_id'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<p><a href='home.php'>← Back to Home</a></p>";
?>
