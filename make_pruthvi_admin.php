<?php
include "c:/xampp/htdocs/student-management-system/config/db.php";

echo "--- 🔍 Searching for 'pruthvi' --- \n";
$query = "SELECT id, name, email, role FROM users WHERE name LIKE '%pruthvi%' OR email LIKE '%pruthvi%'";
$res = mysqli_query($conn, $query);

if (mysqli_num_rows($res) > 0) {
    while($row = mysqli_fetch_assoc($res)) {
        echo "Found: ID: {$row['id']} | Name: {$row['name']} | Email: {$row['email']} | Role: {$row['role']}\n";
        
        // If they are a student, promote them to admin as requested
        if ($row['role'] !== 'admin') {
            echo "🚀 Promoting {$row['name']} to ADMIN...\n";
            $new_pass = password_hash('admin123', PASSWORD_BCRYPT);
            $update = "UPDATE users SET role='admin', password='$new_pass' WHERE id=" . $row['id'];
            if (mysqli_query($conn, $update)) {
                echo "✅ Success! Role set to 'admin' and password reset to 'admin123'\n";
            }
        }
    }
} else {
    echo "❌ No user named 'pruthvi' found. Creating new admin 'pruthvi'...\n";
    $pass = password_hash('admin123', PASSWORD_BCRYPT);
    $ins = "INSERT INTO users (name, email, password, role) VALUES ('pruthvi', 'pruthvi@example.com', '$pass', 'admin')";
    if (mysqli_query($conn, $ins)) {
        echo "✅ Created new Admin: pruthvi@example.com / admin123\n";
    }
}
?>
