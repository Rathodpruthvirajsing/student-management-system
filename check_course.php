<?php
include "config/db.php";
$q = mysqli_query($conn, "SELECT id FROM courses WHERE id=1");
if (mysqli_num_rows($q) > 0) {
    echo "Course 1 exists\n";
} else {
    echo "Course 1 does NOT exist\n";
    $q2 = mysqli_query($conn, "SELECT id FROM courses LIMIT 1");
    if ($r2 = mysqli_fetch_assoc($q2)) {
        echo "Found another course: ID " . $r2['id'] . "\n";
    } else {
        echo "No courses found at all!\n";
    }
}
