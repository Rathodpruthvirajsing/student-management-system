<?php
mysqli_report(MYSQLI_REPORT_OFF);
$c = @mysqli_connect('localhost','root','','student_db');
if (!$c) { die("CONN_FAIL\n"); }

// Check fee table names
$r1 = @mysqli_query($c, "SHOW TABLES LIKE 'fee_structure'");
$r2 = @mysqli_query($c, "SHOW TABLES LIKE 'fee_structures'");
echo "fee_structure (singular): " . (mysqli_num_rows($r1)>0?'EXISTS':'MISSING') . "\n";
echo "fee_structures (plural):  " . (mysqli_num_rows($r2)>0?'EXISTS':'MISSING') . "\n";
if (mysqli_num_rows($r1)>0) {
    echo "\nfee_structure columns:\n";
    $d = @mysqli_query($c,'DESCRIBE fee_structure');
    while($row=mysqli_fetch_assoc($d)) echo "  {$row['Field']} ({$row['Type']})\n";
    $cnt = @mysqli_query($c,'SELECT COUNT(*) as c FROM fee_structure');
    $crw = mysqli_fetch_assoc($cnt);
    echo "  Records: {$crw['c']}\n";
}

echo "\n=== TEACHERS CROSS-CHECK ===\n";
$tu = @mysqli_query($c,'SELECT name,email FROM users WHERE role="teacher"');
while($u = mysqli_fetch_assoc($tu)){
    $chk = @mysqli_query($c,'SELECT id,name,course_id FROM teachers WHERE email="'.$u['email'].'"');
    echo "  user: {$u['email']}\n";
    if (mysqli_num_rows($chk)>0) {
        $t = mysqli_fetch_assoc($chk);
        echo "    -> IN teachers table: [{$t['id']}] {$t['name']}, course_id={$t['course_id']}\n";
    } else {
        echo "    -> NOT IN teachers table! Dashboard will crash on login.\n";
    }
}
$all_t = @mysqli_query($c,'SELECT t.*,c.course_name FROM teachers t LEFT JOIN courses c ON t.course_id=c.id');
echo "\nAll teachers table entries:\n";
while($row=mysqli_fetch_assoc($all_t)){
    $u_chk = @mysqli_query($c,'SELECT id FROM users WHERE email="'.$row['email'].'" AND role="teacher"');
    $has_user = mysqli_num_rows($u_chk) > 0 ? 'has user account' : 'NO USER ACCOUNT!';
    echo "  [{$row['id']}] {$row['name']} | {$row['email']} | course: ".($row['course_name']??'NONE')." | $has_user\n";
}

echo "\n=== PARENTS CROSS-CHECK ===\n";
$pu = @mysqli_query($c,'SELECT name,email FROM users WHERE role="parent"');
while($u = mysqli_fetch_assoc($pu)){
    $chk = @mysqli_query($c,'SELECT id,name,student_id FROM parents WHERE email="'.$u['email'].'"');
    echo "  user: {$u['email']}\n";
    if (mysqli_num_rows($chk)>0) {
        $p = mysqli_fetch_assoc($chk);
        // Check child link
        $s_chk = @mysqli_query($c,"SELECT name,enrollment_no FROM students WHERE id='{$p['student_id']}'");
        if ($s_chk && mysqli_num_rows($s_chk)>0) {
            $s = mysqli_fetch_assoc($s_chk);
            echo "    -> IN parents table, linked to child: {$s['name']} (Enroll: {$s['enrollment_no']})\n";
        } else {
            echo "    -> IN parents table BUT child student_id={$p['student_id']} NOT FOUND in students!\n";
        }
    } else {
        echo "    -> NOT IN parents table! Dashboard will crash.\n";
    }
}

echo "\n=== SIDEBAR ===\n";
$side = file_get_contents('c:/xampp/htdocs/student-management-system/includes/sidebar.php');
echo "teacher references: " . (strpos($side,'teacher')!==false?'FOUND':'MISSING') . "\n";
echo "parent references:  " . (strpos($side,'parent')!==false?'FOUND':'MISSING') . "\n";
// Count teacher links
$teacher_links = preg_match_all('/href=\".*teacher.*\"/', $side, $m);
echo "teacher href links: $teacher_links\n";

echo "\n=== PARENT DASHBOARD SQL ISSUE ===\n";
// Simulate the fee query from parent/dashboard.php
$test_fee = @mysqli_query($c, "SELECT fs.total_fee, COALESCE(SUM(fp.amount_paid), 0) as paid 
    FROM fee_structure fs 
    JOIN students s ON s.course_id = fs.course_id 
    LEFT JOIN fee_payments fp ON fp.student_id = s.id 
    WHERE s.id = 1 GROUP BY fs.total_fee");
if ($test_fee !== false) {
    echo "fee_structure query in parent dashboard: [PASS] Works correctly\n";
} else {
    echo "fee_structure query in parent dashboard: [FAIL] SQL Error: " . mysqli_error($c) . "\n";
}
