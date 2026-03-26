<?php
$conn = @mysqli_connect('localhost', 'root', '', 'student_db');
if(!$conn) die('Connection failed: ' . mysqli_connect_error());

echo "<pre>";
echo "=== USERS TABLE ===\n";
$r = mysqli_query($conn, 'DESCRIBE users');
while($row = mysqli_fetch_assoc($r)) {
    echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . ($row['Default'] ?? 'NULL') . "\n";
}

echo "\n=== CHECK PARENTS TABLE ===\n";
$r2 = mysqli_query($conn, "SHOW TABLES LIKE 'parents'");
if($r2 && mysqli_num_rows($r2) > 0) {
    $r3 = mysqli_query($conn, 'DESCRIBE parents');
    while($row = mysqli_fetch_assoc($r3)) {
        echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . ($row['Default'] ?? 'NULL') . "\n";
    }
    echo "\n=== PARENTS DATA ===\n";
    $pd = mysqli_query($conn, 'SELECT * FROM parents');
    while($row = mysqli_fetch_assoc($pd)) {
        echo json_encode($row) . "\n";
    }
} else {
    echo "parents TABLE DOES NOT EXIST\n";
}

echo "\n=== STUDENTS LIST ===\n";
$s = mysqli_query($conn, 'SELECT id, enrollment_no, name, email FROM students ORDER BY id');
while($row = mysqli_fetch_assoc($s)) {
    echo $row['id'] . ' | ' . $row['enrollment_no'] . ' | ' . $row['name'] . ' | ' . ($row['email'] ?? '') . "\n";
}

echo "\n=== USERS WITH ROLE ===\n";
$u = mysqli_query($conn, 'SELECT id, name, email, role FROM users ORDER BY id');
while($row = mysqli_fetch_assoc($u)) {
    echo $row['id'] . ' | ' . $row['name'] . ' | ' . $row['email'] . ' | ' . $row['role'] . "\n";
}

echo "</pre>";
mysqli_close($conn);
?>
