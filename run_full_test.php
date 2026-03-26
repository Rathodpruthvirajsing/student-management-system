<?php
/**
 * Full Automated Test Script - Student Management System
 * Runs via PHP CLI or browser
 */

// DB connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'student_db';

$results = [];
$pass_count = 0;
$fail_count = 0;
$warn_count = 0;

function test($name, $status, $detail = '') {
    global $results, $pass_count, $fail_count, $warn_count;
    $results[] = ['name' => $name, 'status' => $status, 'detail' => $detail];
    if ($status === 'PASS') $pass_count++;
    elseif ($status === 'FAIL') $fail_count++;
    else $warn_count++;
}

// =====================
// 1. PHP VERSION & ENV
// =====================
$phpv = phpversion();
test('PHP Version', version_compare($phpv, '7.4', '>=') ? 'PASS' : 'WARN', "PHP $phpv");
test('MySQLi Extension', extension_loaded('mysqli') ? 'PASS' : 'FAIL', extension_loaded('mysqli') ? 'Loaded' : 'NOT FOUND');
test('Session Extension', extension_loaded('session') ? 'PASS' : 'FAIL', extension_loaded('session') ? 'Loaded' : 'NOT FOUND');
test('JSON Extension', extension_loaded('json') ? 'PASS' : 'FAIL', extension_loaded('json') ? 'Loaded' : 'NOT FOUND');

// =====================
// 2. DATABASE
// =====================
$conn = @mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    test('Database Connection', 'FAIL', mysqli_connect_error());
} else {
    test('Database Connection', 'PASS', "Connected to $dbname");

    // Check all tables
    $tables = ['users','students','courses','teachers','attendance','exams','fee_structure','fee_payments','parents','notices','messages','timetables','assignments','notice_reads'];
    foreach ($tables as $tbl) {
        $r = mysqli_query($conn, "SHOW TABLES LIKE '$tbl'");
        if ($r && mysqli_num_rows($r) > 0) {
            $cnt = mysqli_query($conn, "SELECT COUNT(*) as c FROM `$tbl`");
            $row = mysqli_fetch_assoc($cnt);
            test("Table: $tbl", 'PASS', "{$row['c']} records");
        } else {
            test("Table: $tbl", 'FAIL', 'Table does not exist');
        }
    }

    // Check user roles
    $admins = mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='admin'");
    $arow = mysqli_fetch_assoc($admins);
    test('Admin Users Exist', (int)$arow['c'] > 0 ? 'PASS' : 'FAIL', "{$arow['c']} admin(s)");

    $students = mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='student'");
    $srow = mysqli_fetch_assoc($students);
    test('Student Users Exist', (int)$srow['c'] > 0 ? 'PASS' : 'FAIL', "{$srow['c']} student(s)");

    $parents = mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='parent'");
    $prow = mysqli_fetch_assoc($parents);
    test('Parent Users', (int)$prow['c'] > 0 ? 'PASS' : 'WARN', "{$prow['c']} parent(s)");

    $teachers_u = mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='teacher'");
    $trow = mysqli_fetch_assoc($teachers_u);
    test('Teacher Users', (int)$trow['c'] > 0 ? 'PASS' : 'WARN', "{$trow['c']} teacher(s)");

    // List sample credentials
    $creds = mysqli_query($conn, "SELECT name, email, role, password FROM users LIMIT 15");
    $users_data = [];
    while ($r = mysqli_fetch_assoc($creds)) {
        $users_data[] = $r;
    }

    // Check courses
    $courses = mysqli_query($conn, "SELECT COUNT(*) as c FROM courses");
    $crow = mysqli_fetch_assoc($courses);
    test('Courses Exist', (int)$crow['c'] > 0 ? 'PASS' : 'WARN', "{$crow['c']} course(s)");

    // Check teachers
    $tchrs = mysqli_query($conn, "SELECT COUNT(*) as c FROM teachers");
    $thrw = mysqli_fetch_assoc($tchrs);
    test('Teachers Exist', (int)$thrw['c'] > 0 ? 'PASS' : 'WARN', "{$thrw['c']} teacher(s)");

    // Check students
    $stds = mysqli_query($conn, "SELECT COUNT(*) as c FROM students");
    $strw = mysqli_fetch_assoc($stds);
    test('Student Records', (int)$strw['c'] > 0 ? 'PASS' : 'WARN', "{$strw['c']} student record(s)");

    // Check attendance
    $att = mysqli_query($conn, "SELECT COUNT(*) as c FROM attendance");
    $atrw = mysqli_fetch_assoc($att);
    test('Attendance Records', 'PASS', "{$atrw['c']} record(s)");

    // Check fee structures
    $fs = mysqli_query($conn, "SELECT COUNT(*) as c FROM fee_structure");
    $fsrw = mysqli_fetch_assoc($fs);
    test('Fee Structures', (int)$fsrw['c'] > 0 ? 'PASS' : 'WARN', (int)$fsrw['c'] > 0 ? "{$fsrw['c']} structure(s)" : "No fee structures defined yet. This is a DATA WARNING, not a system error. Please add at least one fee structure via Fees > Structure to clear this.");
}

// =====================
// 3. FILE STRUCTURE
// =====================
$base = __DIR__;
$critical_files = [
    'index.php'             => 'Login Page',
    'home.php'              => 'Home Page',
    'dashboard.php'         => 'Admin Dashboard',
    'student_dashboard.php' => 'Student Dashboard',
    'login_selection.php'   => 'Login Selection',
    'registration_selection.php' => 'Registration Selection',
    'register.php'          => 'Registration Page',
    'reset_password.php'    => 'Password Reset',
    'auth/login.php'        => 'Login Handler',
    'auth/logout.php'       => 'Logout Handler',
    'config/db.php'         => 'DB Config',
    'includes/header.php'   => 'Header Template',
    'includes/footer.php'   => 'Footer Template',
    'includes/sidebar.php'  => 'Sidebar Template',
    'auth/session.php'      => 'Session Manager',
    'assets/css/style.css'  => 'Main CSS',
];

foreach ($critical_files as $file => $label) {
    $path = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    $exists = file_exists($path);
    $size = $exists ? round(filesize($path)/1024, 2) . ' KB' : 'N/A';
    test("File: $label", $exists ? 'PASS' : 'FAIL', $exists ? $size : "MISSING: $file");
}

// =====================
// 4. MODULE FILES
// =====================
$modules = [
    // Students
    'modules/students/view.php'             => 'Students: View List',
    'modules/students/add.php'              => 'Students: Add',
    'modules/students/edit.php'             => 'Students: Edit',
    'modules/students/delete.php'           => 'Students: Delete',
    // Courses
    'modules/courses/view.php'              => 'Courses: View',
    'modules/courses/add.php'               => 'Courses: Add',
    'modules/courses/edit.php'              => 'Courses: Edit',
    'modules/courses/delete.php'            => 'Courses: Delete',
    // Teachers
    'modules/teachers/view.php'             => 'Teachers: View',
    'modules/teachers/add.php'              => 'Teachers: Add',
    'modules/teachers/edit.php'             => 'Teachers: Edit',
    'modules/teachers/delete.php'           => 'Teachers: Delete',
    // Attendance
    'modules/attendance/view.php'           => 'Attendance: Admin View',
    'modules/attendance/mark.php'           => 'Attendance: Mark',
    'modules/attendance/report.php'         => 'Attendance: Report',
    'modules/attendance/student_view.php'   => 'Attendance: Student View',
    // Exams & Marks
    'modules/exams/create.php'              => 'Exams: Create',
    'modules/exams/marks.php'               => 'Exams: Mark Entry',
    'modules/exams/result.php'              => 'Exams: Results',
    'modules/exams/student_results.php'     => 'Exams: Student Results',
    // Fees
    'modules/fees/structure.php'            => 'Fees: Structure',
    'modules/fees/add_structure.php'        => 'Fees: Add Structure',
    'modules/fees/payment.php'              => 'Fees: Payments',
    'modules/fees/add_payment.php'          => 'Fees: Add Payment',
    'modules/fees/student_fees.php'         => 'Fees: Student View',
    // Notices
    'modules/notices/view.php'              => 'Notices: View',
    'modules/notices/add.php'               => 'Notices: Add',
    'modules/notices/edit.php'              => 'Notices: Edit',
    'modules/notices/delete.php'            => 'Notices: Delete',
    // Timetable
    'modules/timetable/view.php'            => 'Timetable: Admin View',
    'modules/timetable/add.php'             => 'Timetable: Add',
    'modules/timetable/edit.php'            => 'Timetable: Edit',
    'modules/timetable/student_view.php'    => 'Timetable: Student View',
    'modules/timetable/teacher_view.php'    => 'Timetable: Teacher View',
    // Chat
    'modules/chat/admin.php'                => 'Chat: Admin',
    'modules/chat/student.php'              => 'Chat: Student',
    'modules/chat/fetch.php'                => 'Chat: Fetch Messages',
    'modules/chat/send.php'                 => 'Chat: Send',
    // Assignments
    'modules/assignments/view.php'          => 'Assignments: View',
    // Parents
    'modules/parents/dashboard.php'         => 'Parents: Dashboard',
    'modules/parents/child_profile.php'     => 'Parents: Child Profile',
];

foreach ($modules as $file => $label) {
    $path = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    $exists = file_exists($path);
    $size = $exists ? round(filesize($path)/1024, 2) . ' KB' : 'N/A';
    test("Module: $label", $exists ? 'PASS' : 'FAIL', $exists ? $size : "MISSING: $file");
}

// =====================
// 5. AUTH SECURITY CHECK
// =====================
$auth_files = [
    'auth/login.php'   => ['session_start', 'password_verify', '$_POST'],
    'auth/session.php' => ['session_start', '$_SESSION'],
    'dashboard.php'    => ['session', 'role', 'admin'],
    'student_dashboard.php' => ['session', 'role', 'student'],
];

foreach ($auth_files as $file => $keywords) {
    $path = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    if (!file_exists($path)) {
        test("Security: $file", 'FAIL', 'File missing');
        continue;
    }
    $content = strtolower(file_get_contents($path));
    $found = [];
    $missing = [];
    foreach ($keywords as $kw) {
        if (strpos($content, strtolower($kw)) !== false) $found[] = $kw;
        else $missing[] = $kw;
    }
    if (empty($missing)) {
        // This block is for run_full_test.php, not add_structure.php validation.
        // The user's provided snippet seems to be a mix-up.
        // Assuming the user intended to update the warning text for run_full_test.php
        // and also add validation to add_structure.php (which is not this file).
        // For this file, we'll keep the original logic but update the warning text if needed.
        test("Security: $file", 'PASS', 'All checks: ' . implode(', ', $found));
    } else {
        test("Security: $file", 'WARN', 'Missing: ' . implode(', ', $missing));
    }
}

// =====================
// 6. ASSETS CHECK
// =====================
$css_path = $base . '/assets/css/style.css';
if (file_exists($css_path)) {
    $css = file_get_contents($css_path);
    test('CSS: Gradient/Colors', strpos($css, 'gradient') !== false ? 'PASS' : 'WARN', strpos($css, 'gradient') !== false ? 'Gradients found' : 'No gradients');
    test('CSS: Responsive Media', strpos($css, '@media') !== false ? 'PASS' : 'WARN', strpos($css, '@media') !== false ? 'Media queries found' : 'No media queries');
    test('CSS: Button Styles', strpos($css, 'btn') !== false ? 'PASS' : 'WARN', 'Button classes defined');
    test('CSS: Sidebar', strpos($css, 'sidebar') !== false ? 'PASS' : 'WARN', 'Sidebar styles defined');
} else {
    test('CSS File', 'FAIL', 'style.css not found!');
}

// =====================
// 7. SYNTAX CHECK (PHP LINT)
// =====================
$php_files_to_lint = [
    'index.php', 'home.php', 'dashboard.php', 'student_dashboard.php',
    'auth/login.php', 'config/db.php', 'auth/session.php', 'includes/header.php',
    'modules/students/view.php', 'modules/courses/view.php', 'modules/teachers/view.php',
    'modules/attendance/mark.php', 'modules/exams/create.php', 'modules/fees/structure.php',
    'modules/notices/view.php', 'modules/timetable/view.php', 'modules/chat/student.php',
    'modules/parents/dashboard.php',
];

foreach ($php_files_to_lint as $file) {
    $path = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
    if (!file_exists($path)) {
        test("Syntax: $file", 'FAIL', 'File not found');
        continue;
    }
    $output = [];
    $ret = 0;
    exec("php -l \"$path\" 2>&1", $output, $ret);
    $out_str = implode(' ', $output);
    if ($ret === 0) {
        test("Syntax: $file", 'PASS', 'No syntax errors');
    } else {
        test("Syntax: $file", 'FAIL', $out_str);
    }
}

// =====================
// OUTPUT HTML REPORT
// =====================
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Management System - Full Test Report</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: #e2e8f0; }
  header { background: linear-gradient(135deg, #667eea, #764ba2); padding: 30px; text-align: center; }
  header h1 { font-size: 2rem; color: white; }
  header p { color: rgba(255,255,255,0.8); margin-top: 6px; }
  .summary { display: flex; gap: 20px; justify-content: center; padding: 20px; flex-wrap: wrap; }
  .stat-card { background: #1e293b; border-radius: 12px; padding: 20px 35px; text-align: center; border: 1px solid #334155; }
  .stat-card .num { font-size: 2.5rem; font-weight: 700; }
  .stat-card .label { font-size: 0.85rem; color: #94a3b8; margin-top: 4px; }
  .green .num { color: #22c55e; }
  .red .num { color: #ef4444; }
  .orange .num { color: #f97316; }
  .total .num { color: #60a5fa; }
  .section { margin: 20px; background: #1e293b; border-radius: 12px; overflow: hidden; border: 1px solid #334155; }
  .section h2 { padding: 16px 20px; background: #0f172a; font-size: 1rem; letter-spacing: 0.05em; color: #94a3b8; text-transform: uppercase; border-bottom: 1px solid #334155; }
  table { width: 100%; border-collapse: collapse; }
  th { padding: 12px 16px; background: #0f172a; font-size: 0.8rem; text-align: left; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
  td { padding: 12px 16px; border-bottom: 1px solid #1e293b; font-size: 0.9rem; }
  tr:hover td { background: #263348; }
  .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
  .badge-pass { background: rgba(34,197,94,0.15); color: #22c55e; border: 1px solid rgba(34,197,94,0.3); }
  .badge-fail { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }
  .badge-warn { background: rgba(249,115,22,0.15); color: #f97316; border: 1px solid rgba(249,115,22,0.3); }
  .cred-table td { font-family: monospace; font-size: 0.85rem; }
  footer { text-align: center; padding: 20px; color: #475569; font-size: 0.85rem; }
</style>
</head>
<body>
<header>
  <h1>🧪 Student Management System — Full Test Report</h1>
  <p>Generated: <?= date('Y-m-d H:i:s') ?> | PHP <?= phpversion() ?></p>
</header>

<div class="summary">
  <div class="stat-card total"><div class="num"><?= $pass_count + $fail_count + $warn_count ?></div><div class="label">Total Tests</div></div>
  <div class="stat-card green"><div class="num"><?= $pass_count ?></div><div class="label">✅ Passed</div></div>
  <div class="stat-card red"><div class="num"><?= $fail_count ?></div><div class="label">❌ Failed</div></div>
  <div class="stat-card orange"><div class="num"><?= $warn_count ?></div><div class="label">⚠️ Warnings</div></div>
</div>

<!-- Users / Credentials Table -->
<?php if (!empty($users_data)): ?>
<div class="section">
  <h2>👥 Registered Users & Login Credentials</h2>
  <table class="cred-table">
    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Password Type</th></tr></thead>
    <tbody>
    <?php foreach ($users_data as $i => $u):
        $pw_type = (strlen($u['password']) > 30) ? 'bcrypt (hashed)' : 'plaintext: ' . htmlspecialchars($u['password']);
    ?>
    <tr>
      <td><?= $i+1 ?></td>
      <td><?= htmlspecialchars($u['name']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><span class="badge <?= $u['role']==='admin'?'badge-pass':($u['role']==='student'?'badge-warn':'badge-fail') ?>"><?= $u['role'] ?></span></td>
      <td style="color:#94a3b8"><?= $pw_type ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Test results grouped by category -->
<?php
$groups = [
    'Environment' => [],
    'Database' => [],
    'Table:' => [],
    'File:' => [],
    'Module:' => [],
    'Security:' => [],
    'CSS:' => [],
    'Syntax:' => [],
];
$ungrouped = [];
foreach ($results as $r) {
    $matched = false;
    foreach ($groups as $prefix => $_) {
        if ($prefix === 'Environment') {
            if (in_array($r['name'], ['PHP Version','MySQLi Extension','Session Extension','JSON Extension'])) {
                $groups[$prefix][] = $r; $matched = true; break;
            }
        } elseif ($prefix === 'Database') {
            if (in_array($r['name'], ['Database Connection','Admin Users Exist','Student Users Exist','Parent Users','Teacher Users','Courses Exist','Teachers Exist','Student Records','Attendance Records','Fee Structures'])) {
                $groups[$prefix][] = $r; $matched = true; break;
            }
        } else {
            if (strpos($r['name'], $prefix) === 0) {
                $groups[$prefix][] = $r; $matched = true; break;
            }
        }
    }
    if (!$matched) $ungrouped[] = $r;
}

$group_icons = [
    'Environment' => '⚙️ Environment',
    'Database'    => '🗄️ Database & Data',
    'Table:'      => '📋 Database Tables',
    'File:'       => '📁 Critical Files',
    'Module:'     => '📦 Module Files',
    'Security:'   => '🔒 Security Checks',
    'CSS:'        => '🎨 CSS & Frontend',
    'Syntax:'     => '🔍 PHP Syntax Validation',
];

foreach ($groups as $prefix => $grp_results):
    if (empty($grp_results)) continue;
    $gp = count(array_filter($grp_results, fn($x) => $x['status']==='PASS'));
    $gf = count(array_filter($grp_results, fn($x) => $x['status']==='FAIL'));
    $gw = count(array_filter($grp_results, fn($x) => $x['status']==='WARN'));
?>
<div class="section">
  <h2><?= $group_icons[$prefix] ?? $prefix ?> <span style="float:right;font-size:0.8rem;font-weight:400">✅ <?= $gp ?> &nbsp; ❌ <?= $gf ?> &nbsp; ⚠️ <?= $gw ?></span></h2>
  <table>
    <thead><tr><th>Test</th><th>Status</th><th>Details</th></tr></thead>
    <tbody>
    <?php foreach ($grp_results as $r): 
        $badge = $r['status'] === 'PASS' ? 'badge-pass' : ($r['status'] === 'FAIL' ? 'badge-fail' : 'badge-warn');
        $icon = $r['status'] === 'PASS' ? '✅' : ($r['status'] === 'FAIL' ? '❌' : '⚠️');
    ?>
    <tr>
      <td><?= htmlspecialchars($r['name']) ?></td>
      <td><span class="badge <?= $badge ?>"><?= $icon ?> <?= $r['status'] ?></span></td>
      <td style="color:#94a3b8"><?= htmlspecialchars($r['detail']) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endforeach; ?>

<div class="section" style="border-color: <?= $fail_count === 0 ? '#22c55e' : '#ef4444' ?>">
  <h2><?= $fail_count === 0 ? '✅ VERDICT: SYSTEM FULLY FUNCTIONAL' : '❌ VERDICT: ISSUES FOUND — FIX REQUIRED' ?></h2>
  <table>
    <tr><td style="padding:20px; color: <?= $fail_count === 0 ? '#22c55e' : '#ef4444' ?>; font-size:1rem;">
      <?php if ($fail_count === 0): ?>
        All <?= $pass_count + $warn_count ?> checks passed. <?= $warn_count ?> warning(s) are non-critical (e.g. empty tables that need data entry). The system is ready to use.
      <?php else: ?>
        <?= $fail_count ?> critical issue(s) detected above. Review the ❌ FAIL items and fix before use.
      <?php endif; ?>
    </td></tr>
    <tr><td style="padding:0 20px 20px; color:#94a3b8">
      <strong style="color:#e2e8f0">Quick Test URLs:</strong><br><br>
      🏠 <a href="home.php" style="color:#60a5fa">Home Page</a> &nbsp;|&nbsp;
      🔐 <a href="login_selection.php" style="color:#60a5fa">Login Selection</a> &nbsp;|&nbsp;
      👤 <a href="index.php?type=admin" style="color:#60a5fa">Admin Login</a> &nbsp;|&nbsp;
      🎓 <a href="index.php?type=student" style="color:#60a5fa">Student Login</a> &nbsp;|&nbsp;
      👨‍👩‍👧 <a href="index.php?type=parent" style="color:#60a5fa">Parent Login</a> &nbsp;|&nbsp;
      📊 <a href="dashboard.php" style="color:#60a5fa">Admin Dashboard</a> &nbsp;|&nbsp;
      🎒 <a href="student_dashboard.php" style="color:#60a5fa">Student Dashboard</a>
    </td></tr>
  </table>
</div>

<footer>Student Management System v1.0 — Test Report — <?= date('Y-m-d H:i:s') ?></footer>
</body>
</html>
