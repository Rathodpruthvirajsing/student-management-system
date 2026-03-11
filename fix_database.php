<?php
/**
 * DATABASE FIX SCRIPT
 * Run this script once to fix the role ENUM and repair existing student accounts.
 */
include "config/db.php";

echo "<h2>🛠️ Database Repair Tool</h2>";

// 1. Fix the ENUM column
$sql_fix_enum = "ALTER TABLE users MODIFY COLUMN role ENUM('admin','teacher','student') DEFAULT 'student'";
if (mysqli_query($conn, $sql_fix_enum)) {
    echo "<p style='color: green;'>✅ SUCCESS: Updated 'users' table to allow 'student' role.</p>";
} else {
    echo "<p style='color: red;'>❌ ERROR: Could not update table: " . mysqli_error($conn) . "</p>";
}

// 2. Fix existing student accounts that have empty roles
$sql_repair_roles = "UPDATE users u 
                    JOIN students s ON u.email = s.email 
                    SET u.role = 'student' 
                    WHERE u.role = '' OR u.role IS NULL";

if (mysqli_query($conn, $sql_repair_roles)) {
    $affected = mysqli_affected_rows($conn);
    echo "<p style='color: green;'>✅ SUCCESS: Repaired $affected existing student accounts.</p>";
} else {
    echo "<p style='color: red;'>❌ ERROR: Could not repair roles: " . mysqli_error($conn) . "</p>";
}

// 3. Display current user status for verification
echo "<h3>Current Users in Database:</h3>";
$result = mysqli_query($conn, "SELECT id, name, email, role FROM users");
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
while ($row = mysqli_fetch_assoc($result)) {
    $color = ($row['role'] == 'student') ? 'blue' : (($row['role'] == 'admin') ? 'red' : 'black');
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td style='color: $color; font-weight: bold;'>{$row['role']}</td>
          </tr>";
}
echo "</table>";

echo "<br><a href='index.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Back to Login</a>";

mysqli_close($conn);
?>
