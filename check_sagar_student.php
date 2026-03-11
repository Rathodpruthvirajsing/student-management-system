<?php
include "c:/xampp/htdocs/student-management-system/config/db.php";
$query = "SELECT * FROM students WHERE email='sagar123@gmail.com'";
$res = mysqli_query($conn, $query);
if($row = mysqli_fetch_assoc($res)) {
    echo "Student record found for sagar.\n";
    print_r($row);
} else {
    echo "Student record NOT found for sagar.\n";
}
?>
