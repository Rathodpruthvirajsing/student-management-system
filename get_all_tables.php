<?php
include "config/db.php";
$tables = ['users', 'students', 'courses', 'teachers', 'exams', 'attendance', 'fee_payments', 'fee_structures'];
foreach ($tables as $t) {
    echo "TABLE: $t\n";
    $q = mysqli_query($conn, "DESCRIBE $t");
    if ($q) {
        while ($row = mysqli_fetch_assoc($q)) {
            echo "  {$row['Field']} | {$row['Type']} | {$row['Null']} | {$row['Key']} | {$row['Default']}\n";
        }
    } else {
        echo "  (Table not found or error: " . mysqli_error($conn) . ")\n";
    }
    echo "\n";
}
?>
