<?php
include "config/db.php";
$out = "";
$tables = ['users', 'students', 'courses', 'attendance', 'teachers', 'teacher_attendance'];
foreach($tables as $table) {
    $out .= "--- $table ---\n";
    $q = mysqli_query($conn, "DESCRIBE $table");
    while($r = mysqli_fetch_assoc($q)) {
        $out .= "Field: {$r['Field']} | Type: {$r['Type']} | Null: {$r['Null']} | Default: " . ($r['Default'] ?? 'NULL') . "\n";
    }
    $out .= "\n";
}
file_put_contents("table_dump.txt", $out);
echo "Dump written to table_dump.txt\n";
