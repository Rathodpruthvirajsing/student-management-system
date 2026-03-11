<?php
include "config/db.php";

echo "<h2>🛠️ Student Account Auto-Fix Tool</h2>";

// 1. Find mismatched students (User exists but Student record missing)
$query = "SELECT u.id, u.name, u.email FROM users u 
          LEFT JOIN students s ON u.email = s.email 
          WHERE u.role = 'student' AND s.email IS NULL";
$res = mysqli_query($conn, $query);

if (mysqli_num_rows($res) > 0) {
    echo "<h3>Fixing Mismatched Records:</h3>";
    while($user = mysqli_fetch_assoc($res)) {
        $name = mysqli_real_escape_string($conn, $user['name']);
        $email = mysqli_real_escape_string($conn, $user['email']);
        $enrollment = "AUTO-" . substr(md5($email), 0, 6);
        
        // Find a valid course_id
        $course_q = mysqli_query($conn, "SELECT id FROM courses LIMIT 1");
        $course_row = mysqli_fetch_assoc($course_q);
        $course_id = $course_row ? $course_row['id'] : 0;
        
        if ($course_id > 0) {
            $insert = "INSERT INTO students (name, email, enrollment_no, course_id) VALUES ('$name', '$email', '$enrollment', $course_id)";
            if (mysqli_query($conn, $insert)) {
                echo "<p style='color: green;'>✅ Created student record for {$user['email']}</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to create student record for {$user['email']}: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Cannot fix {$user['email']}: No courses found in database.</p>";
        }
    }
} else {
    echo "<p>✅ No mismatched student accounts found.</p>";
}

// 2. Fix roles (ensure everyone in students table has a user account with role 'student')
$query = "SELECT s.id, s.name, s.email FROM students s 
          LEFT JOIN users u ON s.email = u.email 
          WHERE u.id IS NULL";
$res = mysqli_query($conn, $query);

if (mysqli_num_rows($res) > 0) {
    echo "<h3>Creating Missing User Accounts:</h3>";
    while($stu = mysqli_fetch_assoc($res)) {
        $name = mysqli_real_escape_string($conn, $stu['name']);
        $email = mysqli_real_escape_string($conn, $stu['email']);
        $pass = password_hash('student123', PASSWORD_BCRYPT);
        
        $insert = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$pass', 'student')";
        if (mysqli_query($conn, $insert)) {
            echo "<p style='color: green;'>✅ Created user account for {$stu['email']} (Password: student123)</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create user account for {$stu['email']}: " . mysqli_error($conn) . "</p>";
        }
    }
} else {
    echo "<p>✅ All students have user accounts.</p>";
}

echo "<br><a href='index.php'>Go to Login</a>";
?>
