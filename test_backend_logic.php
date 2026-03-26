<?php
include "config/db.php";

echo "--- STARTING SYSTEM TEST ---\n";

// 1. Test Admin User (Existing)
$res = mysqli_query($conn, "SELECT * FROM users WHERE role='admin' LIMIT 1");
if ($row = mysqli_fetch_assoc($res)) {
    echo "[PASS] Admin user exists: " . $row['email'] . "\n";
} else {
    echo "[FAIL] No Admin user found.\n";
}

// 2. Test Student User and Profile
$res = mysqli_query($conn, "SELECT u.email, s.enrollment_no FROM users u JOIN students s ON u.email = s.email WHERE u.role='student' LIMIT 1");
if ($row = mysqli_fetch_assoc($res)) {
    echo "[PASS] Student link valid: " . $row['email'] . " (Enr: " . $row['enrollment_no'] . ")\n";
    $test_enr = $row['enrollment_no'];
} else {
    echo "[FAIL] Student link broken or no students.\n";
    $test_enr = "N/A";
}

// 3. Test Teacher Registration Logic (Simulated)
$test_teacher_email = "test_teacher_" . time() . "@example.com";
$pass = password_hash("pass123", PASSWORD_DEFAULT);
mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('Test Teacher', '$test_teacher_email', '$pass', 'teacher')");
$uid = mysqli_insert_id($conn);
mysqli_query($conn, "INSERT INTO teachers (name, email, phone) VALUES ('Test Teacher', '$test_teacher_email', '1234567890')");

$res = mysqli_query($conn, "SELECT * FROM teachers WHERE email='$test_teacher_email'");
if (mysqli_fetch_assoc($res)) {
    echo "[PASS] Teacher registration logic verified.\n";
} else {
    echo "[FAIL] Teacher table insertion failed.\n";
}

// 4. Test Parent Registration Logic (Simulated)
$test_parent_email = "test_parent_" . time() . "@example.com";
mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('Test Parent', '$test_parent_email', '$pass', 'parent')");
$p_uid = mysqli_insert_id($conn);

// Find a student ID
$s_res = mysqli_query($conn, "SELECT id FROM students LIMIT 1");
$sid = mysqli_fetch_assoc($s_res)['id'] ?? 'NULL';

mysqli_query($conn, "INSERT INTO parents (name, email, student_id) VALUES ('Test Parent', '$test_parent_email', $sid)");

$res = mysqli_query($conn, "SELECT * FROM parents WHERE email='$test_parent_email'");
if ($p_row = mysqli_fetch_assoc($res)) {
    echo "[PASS] Parent registration logic verified. Linked Student ID: " . $p_row['student_id'] . "\n";
} else {
    echo "[FAIL] Parent table insertion failed.\n";
}

echo "--- CLEANUP ---\n";
mysqli_query($conn, "DELETE FROM users WHERE email IN ('$test_teacher_email', '$test_parent_email')");
mysqli_query($conn, "DELETE FROM teachers WHERE email='$test_teacher_email'");
mysqli_query($conn, "DELETE FROM parents WHERE email='$test_parent_email'");

echo "--- TEST COMPLETE ---\n";
?>
