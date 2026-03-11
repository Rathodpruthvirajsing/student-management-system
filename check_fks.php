<?php
include "config/db.php";
$q = mysqli_query($conn, "SELECT 
    TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE
    REFERENCED_TABLE_SCHEMA = 'student_db' AND
    TABLE_NAME = 'students';");
while($r = mysqli_fetch_assoc($q)) {
    print_r($r);
}
