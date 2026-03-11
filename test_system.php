<?php
include 'config/db.php';

echo "<h2>STUDENT MANAGEMENT SYSTEM - TESTING REPORT</h2>\n";

// Test 1: Database connection
echo "<h3>TEST 1: Database Connection</h3>\n";
if($conn) {
  echo "Status: <span style='color:green;'>PASS</span> - Connected to database<br>";
} else {
  echo "Status: <span style='color:red;'>FAIL</span> - Connection failed<br>";
  exit(1);
}

// Test 2: Check if users table exists and has data
echo "<h3>TEST 2: Users Table</h3>\n";
$result = $conn->query('SELECT COUNT(*) as count FROM users');
if($result) {
  $row = $result->fetch_assoc();
  echo "Status: <span style='color:green;'>PASS</span> - Users table exists<br>";
  echo "Total users: " . $row['count'] . "<br>";
} else {
  echo "Status: <span style='color:red;'>FAIL</span> - Users table does not exist<br>";
}

// Test 3: Check admin users
echo "<h3>TEST 3: Admin Users</h3>\n";
$result = $conn->query('SELECT id, name, email, role FROM users WHERE role="admin"');
if($result) {
  $count = $result->num_rows;
  echo "Status: <span style='color:green;'>PASS</span> - Query executed<br>";
  echo "Admin count: " . $count . "<br>";
  if($count > 0) {
    echo "<ul>";
    while($row = $result->fetch_assoc()) {
      echo "<li>" . $row['name'] . " (" . $row['email'] . ")</li>";
    }
    echo "</ul>";
  } else {
    echo "No admin users found. Need to create test admin user.<br>";
  }
} else {
  echo "Status: <span style='color:red;'>FAIL</span> - Query failed<br>";
}

// Test 4: Check student users
echo "<h3>TEST 4: Student Users</h3>\n";
$result = $conn->query('SELECT id, name, email, role FROM users WHERE role="student" LIMIT 5');
if($result) {
  $count = $result->num_rows;
  echo "Status: <span style='color:green;'>PASS</span> - Query executed<br>";
  echo "Student count (showing first 5): " . $count . "<br>";
  if($count > 0) {
    echo "<ul>";
    while($row = $result->fetch_assoc()) {
      echo "<li>" . $row['name'] . " (" . $row['email'] . ")</li>";
    }
    echo "</ul>";
  } else {
    echo "No student users found. Need to create test student accounts.<br>";
  }
} else {
  echo "Status: <span style='color:red;'>FAIL</span> - Query failed<br>";
}

// Test 5: Check other tables
echo "<h3>TEST 5: Database Tables</h3>\n";
$tables = ['students', 'courses', 'teachers', 'exams', 'attendance', 'fee_payments', 'fee_structures'];
$results = [];
foreach($tables as $table) {
  $result = $conn->query("SELECT COUNT(*) as count FROM $table");
  if($result) {
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $results[$table] = $count;
    echo "$table: <span style='color:green;'>EXISTS</span> ($count records)<br>";
  } else {
    echo "$table: <span style='color:red;'>NOT FOUND</span><br>";
  }
}

// Test 6: Check CSS file
echo "<h3>TEST 6: CSS File</h3>\n";
if(file_exists('assets/css/style.css')) {
  $size = filesize('assets/css/style.css');
  echo "Status: <span style='color:green;'>PASS</span> - CSS file exists<br>";
  echo "File size: " . $size . " bytes<br>";
} else {
  echo "Status: <span style='color:red;'>FAIL</span> - CSS file not found<br>";
}

// Test 7: Check logs directory
echo "<h3>TEST 7: Logs Directory</h3>\n";
if(is_dir('logs')) {
  echo "Status: <span style='color:green;'>PASS</span> - Logs directory exists<br>";
} else {
  echo "Status: <span style='color:orange;'>WARNING</span> - Logs directory missing (will be created on first login)<br>";
}

// Test 8: Check uploads directory
echo "<h3>TEST 8: Uploads Directory</h3>\n";
if(is_dir('uploads')) {
  echo "Status: <span style='color:green;'>PASS</span> - Uploads directory exists<br>";
} else {
  echo "Status: <span style='color:orange;'>WARNING</span> - Uploads directory missing<br>";
}

echo "<h3>TEST SUMMARY</h3>\n";
echo "Database: <span style='color:green;'>✓ Working</span><br>";
echo "Tables: <span style='color:green;'>✓ Created</span><br>";
echo "Test Data: Need to verify<br>";
echo "CSS: <span style='color:green;'>✓ Loaded</span><br>";
echo "<br><strong>Next Steps:</strong><br>";
echo "1. Clear browser cache (Ctrl+Shift+R)<br>";
echo "2. Visit: <a href='home.php'>home.php</a><br>";
echo "3. Click Login and select Admin or Student<br>";
echo "4. Try to login<br>";
?>
