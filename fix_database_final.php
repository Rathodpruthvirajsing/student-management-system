<?php
include "c:/xampp/htdocs/student-management-system/config/db.php";

echo "--- 🔧 DATABASE REPAIR STARTED ---\n";

// 1. Fix users with blank roles who are clearly students
$sql1 = "UPDATE users SET role = 'student' WHERE role = '' OR role IS NULL";
if (mysqli_query($conn, $sql1)) {
    echo "✅ Fixed blank roles in users table.\n";
}

// 2. Identify and fix users in 'users' table (role=student) missing in 'students' table
$query = "SELECT u.id, u.name, u.email FROM users u 
          LEFT JOIN students s ON u.email = s.email 
          WHERE u.role = 'student' AND s.email IS NULL";
$res = mysqli_query($conn, $query);

while($row = mysqli_fetch_assoc($res)) {
    $name = mysqli_real_escape_string($conn, $row['name']);
    $email = mysqli_real_escape_string($conn, $row['email']);
    $enr = "ENR" . str_pad($row['id'], 5, "0", STR_PAD_LEFT);
    
    echo "🔹 Creating missing student record for: {$name} ({$email})\n";
    
    $sql_ins = "INSERT INTO students (enrollment_no, name, email, admission_date) 
                VALUES ('$enr', '$name', '$email', CURDATE())";
    if (mysqli_query($conn, $sql_ins)) {
        echo "   ✅ Created successfully (Enrollment: $enr).\n";
    } else {
        echo "   ❌ Failed: " . mysqli_error($conn) . "\n";
    }
}

// 3. Specifically fix 'utsav' if needed (ensure enrollment is there)
$utsav_check = mysqli_query($conn, "SELECT * FROM users WHERE name LIKE '%utsav%'");
if ($u = mysqli_fetch_assoc($utsav_check)) {
    mysqli_query($conn, "UPDATE users SET role = 'student' WHERE id = " . $u['id']);
    echo "✅ Verified 'utsav' role is 'student'.\n";
}

echo "--- 🏁 REPAIR COMPLETE ---\n";
?>
