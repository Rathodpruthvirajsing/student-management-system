<?php
include "config/db.php";
$q = mysqli_query($conn, "SELECT email, course_id FROM students LIMIT 5");
while($r=mysqli_fetch_assoc($q)) {
    print_r($r);
}
echo "TIMETABLES:\n";
$q2 = mysqli_query($conn, "SELECT * FROM timetables LIMIT 5");
while($r=mysqli_fetch_assoc($q2)) {
    print_r($r);
}
?>
