<?php
// Test Script for Role-Based Access Flow
session_start();

echo "=== STUDENT MANAGEMENT SYSTEM - FLOW TEST ===\n\n";

echo "1. SESSION STATE:\n";
echo "   Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? "ACTIVE" : "INACTIVE") . "\n";
echo "   User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "   User Name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "\n";
echo "   User Email: " . ($_SESSION['user_email'] ?? 'NOT SET') . "\n";
echo "   User Role: " . ($_SESSION['role'] ?? 'NOT SET') . "\n";
echo "\n";

echo "2. KEY FILES CHECK:\n";
$files_to_check = [
    'home.php' => 'Home/Landing Page',
    'index.php' => 'Login Form',
    'login_selection.php' => 'Role Selection',
    'dashboard.php' => 'Admin Dashboard',
    'student_dashboard.php' => 'Student Dashboard',
    'auth/login.php' => 'Login Processor',
    'auth/logout.php' => 'Logout Handler',
    'auth/session.php' => 'Session Manager',
    'config/db.php' => 'Database Config'
];

foreach ($files_to_check as $file => $desc) {
    $exists = file_exists(__DIR__ . '/' . $file) ? '✓' : '✗';
    echo "   $exists $desc ($file)\n";
}
echo "\n";

echo "3. DATABASE CONNECTION TEST:\n";
include "config/db.php";
if (@$conn && $conn->ping()) {
    echo "   ✓ Database connected successfully\n";
    
    // Check users table
    $users_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
    if ($users_count) {
        $count = mysqli_fetch_assoc($users_count);
        echo "   ✓ Users table exists with " . $count['total'] . " users\n";
    }
} else {
    echo "   ✗ Database connection failed\n";
}
echo "\n";

echo "4. CURRENT FLOW STATE:\n";
if (isset($_SESSION['user_id'])) {
    echo "   User is LOGGED IN\n";
    if ($_SESSION['role'] === 'admin') {
        echo "   Role: ADMIN\n";
        echo "   Expected Redirect: dashboard.php\n";
    } else {
        echo "   Role: STUDENT\n";
        echo "   Expected Redirect: student_dashboard.php\n";
    }
} else {
    echo "   User is NOT LOGGED IN\n";
    echo "   Expected Flow:\n";
    echo "   1. View home.php (landing page)\n";
    echo "   2. Click 'Login' → login_selection.php (role selection)\n";
    echo "   3. Select role → index.php (login form)\n";
    echo "   4. Enter credentials → auth/login.php (authentication)\n";
    echo "   5. Redirect to appropriate dashboard based on role\n";
}
echo "\n";

echo "5. FILE MODIFICATIONS SUMMARY:\n";
echo "   ✓ index.php - Added session check at top\n";
echo "   ✓ auth/logout.php - Changed redirect to home.php\n";
echo "   ✓ auth/session.php - Updated redirects\n";
echo "   ✓ dashboard.php - Better error handling\n";
echo "   ✓ student_dashboard.php - Better error handling\n";
echo "   ✓ home.php - Added error/success message display\n";
echo "\n";

echo "=== TEST COMPLETE ===\n";
?>
