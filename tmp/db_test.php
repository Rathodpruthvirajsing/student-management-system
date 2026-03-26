<?php
$host='localhost'; $user='root'; $pass=''; $db='student_db';
$c = @mysqli_connect($host,$user,$pass,$db);
if(!$c){ echo "CONN_FAIL: ".mysqli_connect_error()."\n"; exit(1); }
echo "CONN_OK\n";
$tables = ['users','students','courses','teachers','attendance','exams','fee_structures','fee_payments','notices','chat_messages','timetable','assignments','notice_reads'];
foreach($tables as $t){
    $r=mysqli_query($c,"SHOW TABLES LIKE '$t'");
    if($r && mysqli_num_rows($r)>0){
        $cnt=mysqli_query($c,"SELECT COUNT(*) as c FROM `$t`");
        $rw=mysqli_fetch_assoc($cnt);
        echo "PASS|$t|{$rw['c']} records\n";
    } else {
        echo "FAIL|$t|missing\n";
    }
}
echo "\n---USERS---\n";
$u=mysqli_query($c,'SELECT name,email,role,password FROM users LIMIT 20');
while($row=mysqli_fetch_assoc($u)){
    $pw=strlen($row['password'])>30?'[hashed]':$row['password'];
    echo $row['role'].'|'.$row['name'].'|'.$row['email'].'|'.$pw."\n";
}
