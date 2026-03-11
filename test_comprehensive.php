<?php
session_start();
include "config/db.php";

echo "<html><head><title>Project Testing Report</title>";
echo "<link rel='stylesheet' href='assets/css/style.css'>";
echo "<style>
  .test-section { margin: 20px; padding: 20px; background: #f5f5f5; border-radius: 8px; }
  .pass { color: green; font-weight: bold; }
  .fail { color: red; font-weight: bold; }
  .warn { color: orange; font-weight: bold; }
  table { width: 100%; border-collapse: collapse; margin: 10px 0; }
  th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
  th { background: #667eea; color: white; }
</style></head><body>";

echo "<div style='max-width: 1200px; margin: 0 auto;'>";
echo "<h1>🧪 Student Management System - Comprehensive Test Report</h1>";
echo "<hr>";

// ==== TEST 1: Authentication System ====
echo "<div class='test-section'>";
echo "<h2>TEST 1: Authentication System</h2>";

$auth_tests = [];

// Check if session works
$auth_tests['Session Management'] = session_status() === PHP_SESSION_ACTIVE ? 'pass' : 'fail';

// Check if auth folder exists
$auth_tests['Auth Module'] = is_dir('auth') ? 'pass' : 'fail';

// Check if login.php exists
$auth_tests['Login Script'] = file_exists('auth/login.php') ? 'pass' : 'fail';

// Check if session.php exists
$auth_tests['Session Script'] = file_exists('auth/session.php') ? 'pass' : 'fail';

foreach($auth_tests as $test => $status) {
  echo "<p>$test: <span class='" . ($status === 'pass' ? 'pass' : 'fail') . "'>" . strtoupper($status) . "</span></p>";
}

echo "</div>";

// ==== TEST 2: Database & Tables ====
echo "<div class='test-section'>";
echo "<h2>TEST 2: Database & Tables</h2>";

$db_tests = [];

// Test connection
$db_tests['Database Connection'] = $conn ? 'pass' : 'fail';

// Test each table
$tables = ['users', 'students', 'courses', 'teachers', 'exams', 'attendance', 'fee_payments', 'fee_structures'];
$table_data = [];

foreach($tables as $table) {
  $result = $conn->query("SELECT COUNT(*) as count FROM $table");
  if($result) {
    $row = $result->fetch_assoc();
    $table_data[$table] = $row['count'];
    $db_tests[$table . ' Table'] = 'pass';
  } else {
    $table_data[$table] = 0;
    $db_tests[$table . ' Table'] = 'fail';
  }
}

echo "<table>";
echo "<tr><th>Component</th><th>Status</th><th>Details</th></tr>";
foreach($db_tests as $test => $status) {
  $details = isset($table_data[str_replace(' Table', '', $test)]) ? $table_data[str_replace(' Table', '', $test)] . ' records' : '';
  echo "<tr><td>$test</td><td><span class='" . ($status === 'pass' ? 'pass' : 'fail') . "'>" . strtoupper($status) . "</span></td><td>$details</td></tr>";
}
echo "</table>";

echo "</div>";

// ==== TEST 3: Users & Roles ====
echo "<div class='test-section'>";
echo "<h2>TEST 3: Users & Roles</h2>";

$admin_result = $conn->query('SELECT COUNT(*) as count FROM users WHERE role="admin"');
$admin_count = $admin_result->fetch_assoc()['count'];

$student_result = $conn->query('SELECT COUNT(*) as count FROM users WHERE role="student"');
$student_count = $student_result->fetch_assoc()['count'];

echo "<p>Total Admins: <strong>$admin_count</strong></p>";
echo "<p>Total Students: <strong>$student_count</strong></p>";

if($admin_count == 0) {
  echo "<p><span class='warn'>⚠ WARNING: No admin users found!</span> You need to create at least one admin user for the system to work.</p>";
} else {
  echo "<p><span class='pass'>✓ Admin users exist</span></p>";
  echo "<table><tr><th>Name</th><th>Email</th><th>Role</th></tr>";
  $result = $conn->query('SELECT name, email, role FROM users WHERE role="admin" LIMIT 10');
  while($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['name'] . "</td><td>" . $row['email'] . "</td><td>" . $row['role'] . "</td></tr>";
  }
  echo "</table>";
}

if($student_count == 0) {
  echo "<p><span class='warn'>⚠ WARNING: No student users found!</span></p>";
} else {
  echo "<p><span class='pass'>✓ Student users exist</span></p>";
  echo "<p>Sample Students:</p>";
  echo "<table><tr><th>Name</th><th>Email</th><th>Role</th></tr>";
  $result = $conn->query('SELECT name, email, role FROM users WHERE role="student" LIMIT 5');
  while($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['name'] . "</td><td>" . $row['email'] . "</td><td>" . $row['role'] . "</td></tr>";
  }
  echo "</table>";
}

echo "</div>";

// ==== TEST 4: Role-Based Access Control ====
echo "<div class='test-section'>";
echo "<h2>TEST 4: Role-Based Access Control</h2>";

$files_to_check = [
  'dashboard.php' => 'Admin Dashboard',
  'student_dashboard.php' => 'Student Dashboard',
  'modules/students/view.php' => 'Students Module',
  'modules/courses/view.php' => 'Courses Module',
  'modules/attendance/view.php' => 'Attendance Module',
  'modules/exams/create.php' => 'Exams Module',
];

echo "<p>Checking if all page files exist and have role checks:</p>";
echo "<table><tr><th>File</th><th>Exists</th><th>Has Role Check</th></tr>";

foreach($files_to_check as $file => $label) {
  $exists = file_exists($file) ? 'pass' : 'fail';
  $content = file_exists($file) ? file_get_contents($file) : '';
  $has_check = (strpos($content, '\$_SESSION[\'role\']') !== false || strpos($content, 'include \"auth/session.php\"') !== false) ? 'pass' : 'warn';
  echo "<tr><td>$label</td><td><span class='" . ($exists === 'pass' ? 'pass' : 'fail') . "'>" . strtoupper($exists) . "</span></td><td><span class='" . ($has_check === 'pass' ? 'pass' : 'warn') . "'>" . strtoupper($has_check) . "</span></td></tr>";
}
echo "</table>";

echo "</div>";

// ==== TEST 5: CSS & Frontend ====
echo "<div class='test-section'>";
echo "<h2>TEST 5: CSS & Frontend Resources</h2>";

$files = [
  'assets/css/style.css' => 'Main CSS',
  'includes/header.php' => 'Header Template',
  'includes/footer.php' => 'Footer Template',
  'includes/sidebar.php' => 'Sidebar Template',
];

echo "<table><tr><th>File</th><th>Status</th><th>Size</th></tr>";

foreach($files as $file => $label) {
  if(file_exists($file)) {
    $size = filesize($file);
    echo "<tr><td>$label</td><td><span class='pass'>EXISTS</span></td><td>" . round($size / 1024, 2) . " KB</td></tr>";
  } else {
    echo "<tr><td>$label</td><td><span class='fail'>MISSING</span></td><td>-</td></tr>";
  }
}
echo "</table>";

echo "</div>";

// ==== TEST 6: Current Session Info ====
echo "<div class='test-section'>";
echo "<h2>TEST 6: Current Session State</h2>";

if(isset($_SESSION['user_id'])) {
  echo "<p>Current User: <strong>" . $_SESSION['user_name'] . "</strong></p>";
  echo "<p>User Role: <strong>" . $_SESSION['role'] . "</strong></p>";
  echo "<p>Status: <span class='pass'>USER IS LOGGED IN</span></p>";
  echo "<p><a href='auth/logout.php'>Logout</a></p>";
} else {
  echo "<p>Status: <span class='warn'>NO USER LOGGED IN</span></p>";
  echo "<p><a href='home.php'>Go to Home</a> | <a href='login_selection.php'>Go to Login</a></p>";
}

echo "</div>";

// ==== TEST 7: System Performance ====
echo "<div class='test-section'>";
echo "<h2>TEST 7: System Health Check</h2>";

$checks = [
  'PHP Version' => phpversion(),
  'Memory Limit' => ini_get('memory_limit'),
  'Max Execution Time' => ini_get('max_execution_time') . 's',
  'Display Errors' => ini_get('display_errors') ? 'On' : 'Off',
  'Upload Max Size' => ini_get('upload_max_filesize'),
];

echo "<table><tr><th>Check</th><th>Value</th></tr>";
foreach($checks as $check => $value) {
  echo "<tr><td>$check</td><td>$value</td></tr>";
}
echo "</table>";

echo "</div>";

// ==== TEST SUMMARY ====
echo "<div class='test-section' style='background: #e8f5e9;'>";
echo "<h2>📋 TEST SUMMARY & RECOMMENDATIONS</h2>";

$issues = [];
if($admin_count == 0) $issues[] = "❌ No admin users created";
if($student_count == 0) $issues[] = "❌ No student test data";
if(!file_exists('logs')) $issues[] = "⚠️  Logs directory missing";
if(!file_exists('uploads')) $issues[] = "⚠️  Uploads directory missing";

if(count($issues) == 0) {
  echo "<p><span class='pass'>✓ SYSTEM READY FOR TESTING!</span></p>";
  echo "<h3>Quick Test Guide:</h3>";
  echo "<ol>";
  echo "<li>Clear browser cache (Ctrl+Shift+R)</li>";
  echo "<li>Visit <a href='home.php'>Home Page</a></li>";
  echo "<li>Click 'Login' button</li>";
  echo "<li>Select Admin or Student</li>";
  echo "<li>Enter credentials and test login</li>";
  echo "<li>Verify CSS loads on dashboard</li>";
  echo "<li>Test all modules and functionality</li>";
  echo "</ol>";
} else {
  echo "<p><span class='warn'>⚠️  ISSUES FOUND:</span></p>";
  echo "<ul>";
  foreach($issues as $issue) {
    echo "<li>$issue</li>";
  }
  echo "</ul>";
  echo "<p><strong>RECOMMENDATIONS:</strong></p>";
  echo "<ol>";
  echo "<li>Run setup.php to initialize database</li>";
  echo "<li>Create test admin user via database or registration</li>";
  echo "<li>Create test student accounts</li>";
  echo "<li>Verify all modules are accessible</li>";
  echo "</ol>";
}

echo "</div>";

echo "</div></body></html>";
?>
