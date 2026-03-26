<?php
include 'config/db.php';
echo "--- ALL PARENTS IN USERS TABLE ---\n";
$r = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role='parent'");
while($u=mysqli_fetch_assoc($r)) {
    echo "User [ID: {$u['id']}]: {$u['name']} ({$u['email']})\n";
    // Check if in parents table
    $p_check = mysqli_query($conn, "SELECT * FROM parents WHERE email='{$u['email']}'");
    if (mysqli_num_rows($p_check) > 0) {
        $p = mysqli_fetch_assoc($p_check);
        echo "   -> In PARENTS Table: [ID: {$p['id']}] linked to Student ID: " . ($p['student_id'] ?? 'NULL') . "\n";
    } else {
        echo "   -> MISSING from PARENTS Table!\n";
    }
}

echo "\n--- SAMPLE STUDENTS ---\n";
$r = mysqli_query($conn, "SELECT id, name, enrollment_no FROM students LIMIT 5");
while($s=mysqli_fetch_assoc($r)) {
    echo "Student [ID: {$s['id']}]: {$s['name']} ({$s['enrollment_no']})\n";
}
?>
