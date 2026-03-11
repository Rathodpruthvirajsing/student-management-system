<?php
include "config/db.php";

$tables = [];
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

$sql = "-- Student Management System Database Export\n";
$sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $table) {
    // Structure
    $res = mysqli_query($conn, "SHOW CREATE TABLE $table");
    $row = mysqli_fetch_row($res);
    $sql .= "\n\n" . $row[1] . ";\n\n";

    // Data
    $res = mysqli_query($conn, "SELECT * FROM $table");
    while ($row = mysqli_fetch_assoc($res)) {
        $keys = array_keys($row);
        $values = array_values($row);
        $val_str = implode("', '", array_map(function($v) use ($conn) { return mysqli_real_escape_string($conn, $v); }, $values));
        $sql .= "INSERT INTO $table (" . implode(", ", $keys) . ") VALUES ('$val_str');\n";
    }
}

$sql .= "\nSET FOREIGN_KEY_CHECKS=1;";
file_put_contents("database_export.sql", $sql);
echo "Database exported successfully to database_export.sql\n";
?>
