<?php
/**
 * Login Diagnostic Tool
 * Check what's wrong with your login
 */
include "config/db.php";

echo "<link rel='stylesheet' href='assets/css/style.css'>";
echo "<style>
    .diagnostic-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
    }
    .diagnostic-container h2 {
        color: #333;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .check-item {
        padding: 12px;
        margin: 10px 0;
        border-left: 4px solid #ddd;
        border-radius: 4px;
        background: white;
    }
    .check-pass {
        border-left-color: #28a745;
        background: #f0f9f6;
    }
    .check-fail {
        border-left-color: #dc3545;
        background: #fdf6f6;
    }
    .check-pass strong {
        color: #28a745;
    }
    .check-fail strong {
        color: #dc3545;
    }
    .code {
        background: #f5f5f5;
        padding: 10px;
        border-radius: 4px;
        font-family: monospace;
        margin: 10px 0;
        overflow-x: auto;
    }
    .fix-section {
        background: #fff3cd;
        padding: 15px;
        border-radius: 4px;
        margin-top: 20px;
        border-left: 4px solid #ffc107;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background: #007bff;
        color: white;
    }
    button.fix-btn {
        background: #dc3545;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin: 5px;
    }
    button.fix-btn:hover {
        background: #c82333;
    }
</style>";

$check_pass = 0;
$check_total = 0;

echo "<div class='diagnostic-container'>";
echo "<h2>🔍 Login Diagnostic Report</h2>";

// Check 1: Database Connection
$check_total++;
echo "<div class='check-item";
if ($conn) {
    echo " check-pass";
    $check_pass++;
    echo "'><strong>✓ Database Connection</strong> OK";
} else {
    echo " check-fail";
    echo "'><strong>✗ Database Connection</strong> FAILED<br>";
    echo "Error: " . mysqli_connect_error();
}
echo "</div>";

if ($conn) {
    // Check 2: Users table exists
    $check_total++;
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
    if (mysqli_num_rows($table_check) > 0) {
        echo "<div class='check-item check-pass'><strong>✓ Users Table</strong> Exists</div>";
        $check_pass++;
    } else {
        echo "<div class='check-item check-fail'><strong>✗ Users Table</strong> Does not exist<br>Run setup.php first!</div>";
    }
    
    // Check 3: User records exist
    $check_total++;
    $user_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
    $result_count = mysqli_fetch_assoc($user_count);
    $count = $result_count['count'];
    
    if ($count > 0) {
        echo "<div class='check-item check-pass'><strong>✓ Users Found</strong> $count user(s)</div>";
        $check_pass++;
    } else {
        echo "<div class='check-item check-fail'><strong>✗ No Users</strong> No users in database!</div>";
    }
    
    // Check 4: List all users
    echo "<div class='check-item'><strong>📋 All Users in Database:</strong>";
    $all_users = mysqli_query($conn, "SELECT id, name, email, password FROM users");
    if (mysqli_num_rows($all_users) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Password Hash (First 30 chars)</th><th>Status</th></tr>";
        
        while ($user = mysqli_fetch_assoc($all_users)) {
            $pass = $user['password'];
            $pass_display = substr($pass, 0, 30) . "...";
            $is_hashed = (strpos($pass, '$2y$') === 0) ? "✓ Hashed" : "✗ Plain Text";
            $status_class = (strpos($pass, '$2y$') === 0) ? "style='color: green;'" : "style='color: red;'";
            
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['name']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td><code>{$pass_display}</code></td>";
            echo "<td $status_class>$is_hashed</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check 5: Look for specific email
    $check_total++;
    $specific_user = mysqli_query($conn, "SELECT * FROM users WHERE email='pruthvi12@gmail.com'");
    if (mysqli_num_rows($specific_user) > 0) {
        $user = mysqli_fetch_assoc($specific_user);
        echo "<div class='check-item check-pass'><strong>✓ User Found</strong> pruthvi12@gmail.com";
        
        if (strpos($user['password'], '$2y$') === 0) {
            echo "<br><strong style='color: green;'>✓ Password is hashed (CORRECT FORMAT)</strong>";
        } else {
            echo "<br><strong style='color: red;'>✗ Password is PLAIN TEXT (NEEDS HASHING!)</strong><br>";
            echo "Current password in DB: <code>{$user['password']}</code>";
        }
        echo "</div>";
        $check_pass++;
    } else {
        echo "<div class='check-item check-fail'><strong>✗ User Not Found</strong> pruthvi12@gmail.com not in database</div>";
    }
}

echo "<div style='margin-top: 20px; text-align: center; padding: 15px; background: #e7f3ff; border-radius: 4px;'>";
echo "<strong>Diagnostic Score: $check_pass / $check_total checks passed</strong>";
echo "</div>";

// Solution
echo "<div class='fix-section'>";
echo "<h3>🔧 How to Fix</h3>";

if ($conn && isset($user) && strpos($user['password'], '$2y$') !== 0) {
    echo "<p><strong>Problem:</strong> Your password is stored as plain text, not hashed!</p>";
    echo "<p><strong>Solution:</strong> Use the password reset tool to hash it properly.</p>";
    echo "<a href='reset_password.php' style='display: inline-block; background: #007bff; color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; margin-top: 10px;'>→ Go to Password Reset Tool</a>";
} else if ($conn && !isset($user)) {
    echo "<p><strong>Problem:</strong> User pruthvi12@gmail.com not found in database!</p>";
    echo "<p><strong>Solution:</strong> You need to manually insert the user or upload a CSV of users.</p>";
    echo "<p>Or try logging in with default admin:</p>";
    echo "<code>Email: admin@example.com<br>Password: admin123</code>";
} else if (!$conn) {
    echo "<p><strong>Problem:</strong> Cannot connect to database!</p>";
    echo "<p>Check your database credentials in <code>config/db.php</code></p>";
}

echo "</div>";

echo "</div>";

mysqli_close($conn);
?>
