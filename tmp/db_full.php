<?php
$host='localhost'; $user='root'; $pass=''; $db='student_db';
$c = @mysqli_connect($host,$user,$pass,$db);
if(!$c){ echo "CONN_FAIL: ".mysqli_connect_error()."\n"; exit(1); }
echo "=== DATABASE STATUS ===\n";
$tables = ['users','students','courses','teachers','attendance','exams','fee_structures','fee_payments','notices','chat_messages','timetable','assignments','notice_reads'];
foreach($tables as $t){
    $r=@mysqli_query($c,"SHOW TABLES LIKE '$t'");
    if($r && mysqli_num_rows($r)>0){
        $cnt=@mysqli_query($c,"SELECT COUNT(*) as c FROM `$t`");
        $rw=mysqli_fetch_assoc($cnt);
        echo "  [PASS] $t - {$rw['c']} records\n";
    } else {
        echo "  [FAIL] $t - MISSING TABLE\n";
    }
}

echo "\n=== ALL USERS ===\n";
$u=mysqli_query($c,'SELECT name,email,role,password FROM users ORDER BY role');
echo str_pad("ROLE",10) . str_pad("NAME",20) . str_pad("EMAIL",38) . "PASSWORD\n";
echo str_repeat("-",90) . "\n";
while($row=mysqli_fetch_assoc($u)){
    $pw=strlen($row['password'])>30?'[bcrypt-hashed]':$row['password'];
    echo str_pad($row['role'],10) . str_pad($row['name'],20) . str_pad($row['email'],38) . $pw . "\n";
}

echo "\n=== COURSES ===\n";
$cr=mysqli_query($c,'SELECT id, course_name, course_code FROM courses');
while($row=mysqli_fetch_assoc($cr)) echo "  [{$row['id']}] {$row['course_name']} ({$row['course_code']})\n";

echo "\n=== TEACHERS ===\n";
$tr=mysqli_query($c,'SELECT t.name, t.email, c.course_name FROM teachers t LEFT JOIN courses c ON t.course_id=c.id');
while($row=mysqli_fetch_assoc($tr)) echo "  {$row['name']} <{$row['email']}> -> {$row['course_name']}\n";

echo "\n=== STUDENTS (first 10) ===\n";
$sr=mysqli_query($c,'SELECT s.name, u.email, c.course_name FROM students s LEFT JOIN users u ON s.user_id=u.id LEFT JOIN courses c ON s.course_id=c.id LIMIT 10');
while($row=mysqli_fetch_assoc($sr)) echo "  {$row['name']} <{$row['email']}> -> {$row['course_name']}\n";

echo "\n=== NOTICES ===\n";
$nr=mysqli_query($c,'SELECT COUNT(*) as c FROM notices');
$nrw=mysqli_fetch_assoc($nr);
echo "  Total notices: {$nrw['c']}\n";

echo "\n=== TIMETABLE ===\n";
$ttr=mysqli_query($c,'SELECT COUNT(*) as c FROM timetable');
$ttrw=mysqli_fetch_assoc($ttr);
echo "  Total timetable entries: {$ttrw['c']}\n";

echo "\n=== ASSIGNMENTS ===\n";
$ar=mysqli_query($c,'SELECT COUNT(*) as c FROM assignments');
$arw=mysqli_fetch_assoc($ar);
echo "  Total assignments: {$arw['c']}\n";

echo "\n=== FEE STRUCTURES ===\n";
$fsr=@mysqli_query($c,'SELECT course_name, amount FROM fee_structures LIMIT 5');
if($fsr && mysqli_num_rows($fsr)>0){
    while($row=mysqli_fetch_assoc($fsr)) echo "  {$row['course_name']}: Rs. {$row['amount']}\n";
} else {
    echo "  No fee structures defined yet\n";
}

echo "\n=== FEE PAYMENTS ===\n";
$fpr=@mysqli_query($c,'SELECT COUNT(*) as c, SUM(amount_paid) as total FROM fee_payments');
$fprw=mysqli_fetch_assoc($fpr);
echo "  Total payments: {$fprw['c']}, Amount: Rs. ".number_format($fprw['total'],0)."\n";

echo "\n=== ATTENDANCE SUMMARY ===\n";
$atr=@mysqli_query($c,'SELECT status, COUNT(*) as c FROM attendance GROUP BY status');
while($row=mysqli_fetch_assoc($atr)) echo "  {$row['status']}: {$row['c']} records\n";
