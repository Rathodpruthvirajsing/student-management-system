<?php
/**
 * Student Login System - Verification Tool
 * This page helps verify that the student login system is working correctly
 */
include "config/db.php";
session_start();

$test_results = [];
$all_pass = true;

// Test 1: Database connection
$test_results['db'] = mysqli_connect_errno() === 0;
if (!$test_results['db']) $all_pass = false;

// Test 2: Users table exists
$users_check = mysqli_query($conn, "SELECT 1 FROM users LIMIT 1");
$test_results['users_table'] = $users_check !== false;
if (!$test_results['users_table']) $all_pass = false;

// Test 3: Students table exists
$students_check = mysqli_query($conn, "SELECT 1 FROM students LIMIT 1");
$test_results['students_table'] = $students_check !== false;
if (!$test_results['students_table']) $all_pass = false;

// Test 4: Check for student users
$student_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='student'"));
$student_users_exist = $student_users['count'] > 0;
$test_results['student_users'] = $student_users_exist;

// Test 5: Check for students
$students_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM students"));
$students_exist = $students_count['count'] > 0;
$test_results['students'] = $students_exist;

// Test 6: Check matching emails
$matching = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) as count FROM students s 
    INNER JOIN users u ON s.email = u.email 
    WHERE u.role = 'student'
"));
$matching_count = $matching['count'];

// Test 7: Logs directory exists
$logs_exist = is_dir(__DIR__ . '/logs');
$test_results['logs_dir'] = $logs_exist;

// Test 8: Uploads directory exists
$uploads_exist = is_dir(__DIR__ . '/uploads/student_photos');
$test_results['uploads_dir'] = $uploads_exist;

// Test 9: CSS file exists
$css_exists = file_exists(__DIR__ . '/assets/css/style.css');
$test_results['css'] = $css_exists;

// Test 10: Session working
$test_results['session'] = isset($_SESSION);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Login System - Verification</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .verification-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .verification-container h1 {
            color: #2c3e50;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        .test-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin: 10px 0;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #ddd;
        }
        .test-item.pass {
            border-left-color: #28a745;
            background: #f1f9f1;
        }
        .test-item.fail {
            border-left-color: #dc3545;
            background: #fff1f1;
        }
        .test-pass {
            color: #28a745;
            font-weight: 600;
            font-size: 18px;
        }
        .test-fail {
            color: #dc3545;
            font-weight: 600;
            font-size: 18px;
        }
        .stats-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            color: #0066cc;
        }
        .status-pass {
            color: #28a745;
            font-weight: 600;
            padding: 10px;
            background: #d4edda;
            border-radius: 4px;
            text-align: center;
            margin: 15px 0;
            font-size: 16px;
        }
        .status-fail {
            color: #dc3545;
            font-weight: 600;
            padding: 10px;
            background: #f8d7da;
            border-radius: 4px;
            text-align: center;
            margin: 15px 0;
            font-size: 16px;
        }
        .next-steps {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
            color: #856404;
        }
    </style>
</head>
<body style="background: #f4f6f9;">
    <div class="verification-container">
        <h1>✅ Student Login System Verification</h1>
        
        <?php if($all_pass && $student_users_exist): ?>
            <div class="status-pass">
                ✅ All Systems Operational - Student Login Ready!
            </div>
        <?php else: ?>
            <div class="status-fail">
                ⚠️ Some checks failed - see details below
            </div>
        <?php endif; ?>

        <h2 style="margin-top: 30px; color: #2c3e50;">System Tests</h2>

        <div class="test-item <?php echo ($test_results['db'] ? 'pass' : 'fail'); ?>">
            <span>Database Connection</span>
            <span class="<?php echo ($test_results['db'] ? 'test-pass' : 'test-fail'); ?>">
                <?php echo ($test_results['db'] ? '✓ PASS' : '✗ FAIL'); ?>
            </span>
        </div>

        <div class="test-item <?php echo ($test_results['users_table'] ? 'pass' : 'fail'); ?>">
            <span>Users Table Exists</span>
            <span class="<?php echo ($test_results['users_table'] ? 'test-pass' : 'test-fail'); ?>">
                <?php echo ($test_results['users_table'] ? '✓ PASS' : '✗ FAIL'); ?>
            </span>
        </div>

        <div class="test-item <?php echo ($test_results['students_table'] ? 'pass' : 'fail'); ?>">
            <span>Students Table Exists</span>
            <span class="<?php echo ($test_results['students_table'] ? 'test-pass' : 'test-fail'); ?>">
                <?php echo ($test_results['students_table'] ? '✓ PASS' : '✗ FAIL'); ?>
            </span>
        </div>

        <div class="test-item <?php echo ($test_results['logs_dir'] ? 'pass' : 'fail'); ?>">
            <span>Logs Directory</span>
            <span class="<?php echo ($test_results['logs_dir'] ? 'test-pass' : 'test-fail'); ?>">
                <?php echo ($test_results['logs_dir'] ? '✓ PASS' : '✗ FAIL'); ?>
            </span>
        </div>

        <div class="test-item <?php echo ($test_results['uploads_dir'] ? 'pass' : 'fail'); ?>">
            <span>Uploads Directory</span>
            <span class="<?php echo ($test_results['uploads_dir'] ? 'test-pass' : 'test-fail'); ?>">
                <?php echo ($test_results['uploads_dir'] ? '✓ PASS' : '✗ FAIL'); ?>
            </span>
        </div>

        <div class="test-item <?php echo ($test_results['css'] ? 'pass' : 'fail'); ?>">
            <span>CSS File</span>
            <span class="<?php echo ($test_results['css'] ? 'test-pass' : 'test-fail'); ?>">
                <?php echo ($test_results['css'] ? '✓ PASS' : '✗ FAIL'); ?>
            </span>
        </div>

        <h2 style="margin-top: 30px; color: #2c3e50;">Data Statistics</h2>

        <div class="stats-box">
            <strong>Student Users:</strong> <?php echo $student_users['count']; ?> accounts<br>
            <strong>Students Records:</strong> <?php echo $students_count['count']; ?> records<br>
            <strong>Matched Pairs:</strong> <?php echo $matching_count; ?> (email match)<br>
            <br>
            <?php if($matching_count > 0): ?>
                <span style="color: #28a745; font-weight: 600;">✓ Students have both user and student records!</span>
            <?php else: ?>
                <span style="color: #dc3545; font-weight: 600;">✗ Need to create student records or user accounts</span>
            <?php endif; ?>
        </div>

        <h2 style="margin-top: 30px; color: #2c3e50;">Quick Actions</h2>

        <div style="margin: 15px 0;">
            <a href="index.php?type=student" class="btn btn-login" style="display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;">
                🔐 Test Student Login
            </a>
            <a href="modules/students/add.php" class="btn btn-add" style="display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;">
                ➕ Add Test Student
            </a>
            <a href="STUDENT_LOGIN_FIX.md" class="btn btn-info" style="display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px;">
                📖 Read Fix Guide
            </a>
        </div>

        <div class="next-steps">
            <strong>Next Steps:</strong><br>
            1️⃣ Make sure ✓ PASS appears for all tests above<br>
            2️⃣ Go to Admin Dashboard → Students → Add New Student<br>
            3️⃣ Fill form with email and password<br>
            4️⃣ Save student<br>
            5️⃣ Try logging in as that student<br>
            6️⃣ ✅ You should see the Student Dashboard
        </div>
    </div>
</body>
</html>
