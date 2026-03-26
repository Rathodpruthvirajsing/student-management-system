<?php
include 'config/db.php';

// Find a student to link to
$res = mysqli_query($conn, "SELECT id FROM students LIMIT 1");
if (mysqli_num_rows($res) == 0) {
    die("No students found. Add one first.");
}
$sid = mysqli_fetch_assoc($res)['id'];

// Find all parents in the 'users' table
$r = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role='parent'");
while($u=mysqli_fetch_assoc($r)) {
    $email = $u['email'];
    $name = $u['name'];
    
    // Check if in parents table
    $p_check = mysqli_query($conn, "SELECT * FROM parents WHERE email='$email'");
    if (mysqli_num_rows($p_check) == 0) {
        $sql = "INSERT INTO parents (name, email, student_id) VALUES ('$name', '$email', $sid)";
        if (mysqli_query($conn, $sql)) {
            echo "INSERTED parent record for $email linked to student ID $sid\n";
        }
    } else {
        $sql = "UPDATE parents SET student_id = $sid WHERE email='$email' AND (student_id IS NULL OR student_id = 0)";
        if (mysqli_query($conn, $sql)) {
            echo "UPDATED parent record for $email linked to student ID $sid\n";
        }
    }
}
?>
