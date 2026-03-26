<?php
include 'config/db.php';

// Find a default student to link (for testing purposes)
$res = mysqli_query($conn, "SELECT id FROM students LIMIT 1");
if (mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    $default_student_id = $row['id'];
    
    // Update all parents that have NULL student_id
    $sql = "UPDATE parents SET student_id = $default_student_id WHERE student_id IS NULL OR student_id = 0";
    if (mysqli_query($conn, $sql)) {
        echo "✓ Successfully linked parents with missing students to Student ID: $default_student_id\n";
    } else {
        echo "Error updating parents: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "No students found in the database. Please add a student first.\n";
}
?>
