<?php
session_start();
include "config/db.php";

echo "<pre>";
echo "Session User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "Session Role: " . ($_SESSION['role'] ?? 'NOT SET') . "\n";
echo "Session Name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "\n";

// Try to get student from database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $student_query = "SELECT s.id, s.email, s.name FROM students s 
                      WHERE s.email = (SELECT email FROM users WHERE id='$user_id')";
    $result = mysqli_query($conn, $student_query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        echo "Student found in database:\n";
        $student = mysqli_fetch_assoc($result);
        print_r($student);
    } else {
        echo "ERROR: Student NOT found in database for this user\n";
        echo "Query: $student_query\n";
        echo "DB Error: " . mysqli_error($conn) . "\n";
    }
}

echo "</pre>";
?>
