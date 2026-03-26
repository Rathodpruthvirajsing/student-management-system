<?php
include "config/db.php";
$res = mysqli_query($conn, "SELECT enrollment_no FROM students LIMIT 1");
if ($row = mysqli_fetch_assoc($res)) {
    echo "STUDENT_ID:" . $row['enrollment_no'];
} else {
    echo "NO_STUDENT";
}
?>
