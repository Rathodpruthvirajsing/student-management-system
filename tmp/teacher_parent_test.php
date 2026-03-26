<?php
mysqli_report(MYSQLI_REPORT_OFF);
$c = @mysqli_connect('localhost','root','','student_db');
if (!$c) { die("CONN_FAIL\n"); }

echo "=== TEACHER & PARENT DATABASE TEST ===\n\n";

// ---- TEACHER TESTS ----
echo "--- TEACHER ---\n";

// Check 'parents' table exists
$r = @mysqli_query($c, "SHOW TABLES LIKE 'parents'");
echo ($r && mysqli_num_rows($r) > 0 ? "[PASS]" : "[FAIL]") . " Table 'parents' exists\n";

// Check 'marks' table (used by teacher performance)
$r2 = @mysqli_query($c, "SHOW TABLES LIKE 'marks'");
echo ($r2 && mysqli_num_rows($r2) > 0 ? "[PASS]" : "[FAIL]") . " Table 'marks' exists (for teacher performance analytics)\n";

// Teachers in the teachers table
$tr = @mysqli_query($c, "SELECT t.id, t.name, t.email, t.course_id, c.course_name FROM teachers t LEFT JOIN courses c ON t.course_id=c.id");
if ($tr && mysqli_num_rows($tr) > 0) {
    echo "[PASS] Teachers in 'teachers' table:\n";
    while ($row = mysqli_fetch_assoc($tr)) {
        echo "       - [{$row['id']}] {$row['name']} | {$row['email']} | Course: " . ($row['course_name'] ?? 'NOT ASSIGNED') . "\n";
    }
} else {
    echo "[FAIL] No teachers in 'teachers' table\n";
}

// Teacher user in 'users' table
$tu = @mysqli_query($c, "SELECT name, email, role, password FROM users WHERE role='teacher'");
if ($tu && mysqli_num_rows($tu) > 0) {
    echo "[PASS] Teacher users in 'users' table:\n";
    while ($row = mysqli_fetch_assoc($tu)) {
        $pw = strlen($row['password']) > 30 ? '[bcrypt]' : $row['password'];
        echo "       - {$row['name']} | {$row['email']} | password: $pw\n";
    }
} else {
    echo "[FAIL] No teacher users in 'users' table\n";
}

// Check if teacher email in users matches email in teachers table
echo "\n[CHECK] Cross-referencing teacher emails...\n";
$match_query = @mysqli_query($c, "SELECT u.email, u.name FROM users u WHERE u.role='teacher'");
while ($u_row = mysqli_fetch_assoc($match_query)) {
    $check = @mysqli_query($c, "SELECT id FROM teachers WHERE email='" . mysqli_real_escape_string($c, $u_row['email']) . "'");
    if ($check && mysqli_num_rows($check) > 0) {
        echo "       [PASS] {$u_row['email']} -> Found in teachers table ✓\n";
    } else {
        echo "       [FAIL] {$u_row['email']} -> NOT in teachers table (login will show error)\n";
    }
}

// Teacher sidebar links: check for teachers/view.php etc
echo "\n[CHECK] Teacher module files:\n";
$teacher_files = [
    'modules/teachers/dashboard.php',
    'modules/teachers/view.php',
    'modules/teachers/add.php',
    'modules/teachers/edit.php',
    'modules/teachers/delete.php',
    'modules/teachers/assign.php',
    'modules/teachers/students_coursewise.php',
    'modules/teachers/student_performance.php',
];
$base = dirname(__FILE__) . '/../../';
foreach ($teacher_files as $f) {
    $path = $base . str_replace('/', DIRECTORY_SEPARATOR, $f);
    $exists = file_exists($path);
    // PHP lint
    $lint = 'N/A';
    if ($exists && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
        $out = []; $ret = 0;
        exec("php -l \"$path\" 2>&1", $out, $ret);
        $lint = $ret === 0 ? 'OK' : 'SYNTAX_ERROR';
    }
    echo "       " . ($exists ? "[PASS]" : "[FAIL]") . " $f | Syntax: $lint\n";
}

// ---- PARENT TESTS ----
echo "\n--- PARENT ---\n";

// parents table
$pr = @mysqli_query($c, "SHOW TABLES LIKE 'parents'");
echo ($pr && mysqli_num_rows($pr) > 0 ? "[PASS]" : "[FAIL]") . " Table 'parents' exists\n";

if ($pr && mysqli_num_rows($pr) > 0) {
    $pr_cnt = @mysqli_query($c, "SELECT COUNT(*) as c FROM parents");
    $cnt = mysqli_fetch_assoc($pr_cnt)['c'];
    echo "[INFO] parents table has $cnt record(s)\n";

    // Show parents rows
    $pr_data = @mysqli_query($c, "SELECT p.*, s.name as child_name FROM parents p LEFT JOIN students s ON p.student_id=s.id LIMIT 10");
    if ($pr_data && mysqli_num_rows($pr_data) > 0) {
        echo "[PASS] Parent records:\n";
        while ($row = mysqli_fetch_assoc($pr_data)) {
            echo "       - {$row['name']} | {$row['email']} | Child: " . ($row['child_name'] ?? 'NOT LINKED') . "\n";
        }
    } else {
        echo "[WARN] No records in parents table\n";
    }
}

// Parent user in users table
$pu = @mysqli_query($c, "SELECT name, email, role, password FROM users WHERE role='parent'");
if ($pu && mysqli_num_rows($pu) > 0) {
    echo "[PASS] Parent users in 'users' table:\n";
    while ($row = mysqli_fetch_assoc($pu)) {
        $pw = strlen($row['password']) > 30 ? '[bcrypt]' : $row['password'];
        echo "       - {$row['name']} | {$row['email']} | password: $pw\n";
    }
} else {
    echo "[FAIL] No parent users in 'users' table\n";
}

// Check if parent email in users matches email in parents table
echo "\n[CHECK] Cross-referencing parent emails...\n";
$pm = @mysqli_query($c, "SELECT email, name FROM users WHERE role='parent'");
while ($p_row = mysqli_fetch_assoc($pm)) {
    $pcheck = @mysqli_query($c, "SELECT id FROM parents WHERE email='" . mysqli_real_escape_string($c, $p_row['email']) . "'");
    if ($pcheck && mysqli_num_rows($pcheck) > 0) {
        echo "       [PASS] {$p_row['email']} -> Found in parents table ✓\n";
    } else {
        echo "       [FAIL] {$p_row['email']} -> NOT in parents table (dashboard will crash)\n";
    }
}

// Parent module files
echo "\n[CHECK] Parent module files:\n";
$parent_files = [
    'modules/parents/dashboard.php',
    'modules/parents/child_profile.php',
];
foreach ($parent_files as $f) {
    $path = $base . str_replace('/', DIRECTORY_SEPARATOR, $f);
    $exists = file_exists($path);
    $lint = 'N/A';
    if ($exists && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
        $out = []; $ret = 0;
        exec("php -l \"$path\" 2>&1", $out, $ret);
        $lint = $ret === 0 ? 'OK' : 'SYNTAX_ERROR';
    }
    echo "       " . ($exists ? "[PASS]" : "[FAIL]") . " $f | Syntax: $lint\n";
}

// Check SQL used in parent dashboard for potential issues
echo "\n[CHECK] Parent dashboard SQL check (fee_structure table name):\n";
$fs_check = @mysqli_query($c, "SHOW TABLES LIKE 'fee_structure'");
$fs_check2 = @mysqli_query($c, "SHOW TABLES LIKE 'fee_structures'");
$fs_exists = ($fs_check && mysqli_num_rows($fs_check) > 0);
$fss_exists = ($fs_check2 && mysqli_num_rows($fs_check2) > 0);
echo "       fee_structure (singular):  " . ($fs_exists ? "[EXISTS]" : "[MISSING]") . "\n";
echo "       fee_structures (plural):   " . ($fss_exists ? "[EXISTS]" : "[MISSING]") . "\n";
if (!$fs_exists && $fss_exists) {
    echo "       [WARN] parent/dashboard.php queries 'fee_structure' (singular) but table is 'fee_structures' (plural)!\n";
    echo "       [WARN] This will cause a SQL ERROR on the parent dashboard fees section.\n";
}

// Check sidebar for teachers/parents — what links exist
echo "\n[CHECK] Sidebar role-based links...\n";
$sidebar = file_get_contents($base . 'includes/sidebar.php');
echo "       Teacher sidebar section: " . (strpos($sidebar, "teacher") !== false ? "[PASS] Found 'teacher' references" : "[FAIL] No teacher section in sidebar") . "\n";
echo "       Parent sidebar section:  " . (strpos($sidebar, "parent") !== false ? "[PASS] Found 'parent' references" : "[FAIL] No parent section in sidebar") . "\n";

echo "\n=== DONE ===\n";
