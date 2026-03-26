<?php
$conn = mysqli_connect('localhost','root','','student_db');
$tables = ['users','students','courses','teachers','attendance','exams','fee_structure','fee_payments','parents','notices','chat_messages','timetable','assignments','notice_reads','marks','leaves','quizzes','assignment_submissions'];
foreach ($tables as $t) {
    $r = mysqli_query($conn, "SHOW TABLES LIKE '$t'");
    $exists = $r && mysqli_num_rows($r) > 0;
    echo $t . ': ' . ($exists ? 'EXISTS' : 'MISSING') . PHP_EOL;
}
// Also check any file missing from the test
$missing_files = [];
$check_files = [
    'modules/parents/child_profile.php',
    'includes/session.php',
    'modules/teachers/view.php',
    'modules/teachers/add.php',
    'modules/teachers/edit.php',
    'modules/teachers/delete.php',
];
foreach ($check_files as $f) {
    echo "File $f: " . (file_exists(__DIR__ . '/../' . $f) ? 'EXISTS' : 'MISSING') . PHP_EOL;
}
?>
