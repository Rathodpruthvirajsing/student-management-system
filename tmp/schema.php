<?php
mysqli_report(MYSQLI_REPORT_OFF);
$c=@mysqli_connect('localhost','root','','student_db');
if(!$c){die("CONN_FAIL\n");}
// Students table structure
$r=@mysqli_query($c,'DESCRIBE students');
echo "=== STUDENTS TABLE ===\n";
while($row=@mysqli_fetch_assoc($r)) echo "  ".$row['Field']." (".$row['Type'].")\n";
// Users
$r2=@mysqli_query($c,'DESCRIBE users');
echo "\n=== USERS TABLE ===\n";
while($row=@mysqli_fetch_assoc($r2)) echo "  ".$row['Field']." (".$row['Type'].")\n";
// Fee structures
$r3=@mysqli_query($c,'DESCRIBE fee_structures');
echo "\n=== FEE_STRUCTURES TABLE ===\n";
while($row=@mysqli_fetch_assoc($r3)) echo "  ".$row['Field']." (".$row['Type'].")\n";
// All users
echo "\n=== ALL USERS ===\n";
$r4=@mysqli_query($c,'SELECT name,email,role,password FROM users ORDER BY role');
while($row=@mysqli_fetch_assoc($r4)){
    $pw=strlen($row['password'])>30?'[bcrypt]':$row['password'];
    echo "  [{$row['role']}] {$row['name']} | {$row['email']} | $pw\n";
}
