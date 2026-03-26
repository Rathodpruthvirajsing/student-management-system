<?php
// Full system test - FILE checks only (no DB connection needed)
$base = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..';

$critical_files = [
    'index.php', 'home.php', 'dashboard.php', 'student_dashboard.php',
    'login_selection.php', 'registration_selection.php', 'register.php',
    'reset_password.php', 'auth/login.php', 'auth/logout.php',
    'config/db.php', 'includes/header.php', 'includes/footer.php',
    'includes/sidebar.php', 'includes/session.php', 'assets/css/style.css',
];

$modules = [
    'modules/students/view.php', 'modules/students/add.php',
    'modules/students/edit.php', 'modules/students/delete.php',
    'modules/courses/view.php', 'modules/courses/add.php',
    'modules/courses/edit.php', 'modules/courses/delete.php',
    'modules/teachers/view.php', 'modules/teachers/add.php',
    'modules/teachers/delete.php',
    'modules/attendance/view.php', 'modules/attendance/mark.php',
    'modules/attendance/report.php', 'modules/attendance/student_view.php',
    'modules/exams/create.php', 'modules/exams/marks.php',
    'modules/exams/result.php', 'modules/exams/student_results.php',
    'modules/fees/structure.php', 'modules/fees/add_structure.php',
    'modules/fees/payment.php', 'modules/fees/add_payment.php',
    'modules/fees/student_fees.php',
    'modules/notices/view.php', 'modules/notices/add.php',
    'modules/notices/edit.php', 'modules/notices/delete.php',
    'modules/timetable/view.php', 'modules/timetable/add.php',
    'modules/timetable/edit.php', 'modules/timetable/student_view.php',
    'modules/timetable/teacher_view.php',
    'modules/chat/admin.php', 'modules/chat/student.php',
    'modules/chat/fetch.php', 'modules/chat/send.php',
    'modules/parents/dashboard.php', 'modules/parents/child_profile.php',
];

$all_files = array_merge($critical_files, $modules);
$pass = 0; $fail = 0;

foreach ($all_files as $f) {
    $path = $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $f);
    if (file_exists($path)) {
        $size = round(filesize($path) / 1024, 2);
        // PHP lint
        if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $out = []; $ret = 0;
            exec("php -l \"$path\" 2>&1", $out, $ret);
            $lint = $ret === 0 ? 'OK' : 'SYNTAX_ERROR: ' . implode(' ', $out);
        } else {
            $lint = 'N/A';
        }
        echo "PASS|$f|{$size}KB|$lint\n";
        $pass++;
    } else {
        echo "FAIL|$f|MISSING\n";
        $fail++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "PASS: $pass\n";
echo "FAIL: $fail\n";
echo "TOTAL: " . ($pass + $fail) . "\n";
