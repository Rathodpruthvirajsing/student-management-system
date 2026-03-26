<?php
include "config/db.php";

$sql = "INSERT INTO timetables (course_id, day_of_week, start_time, end_time, subject, room_number, teacher_name) VALUES 
(3, 'Monday', '09:00:00', '10:30:00', 'Introduction to HTML/CSS', 'Lab 1', 'Amit Patval'),
(3, 'Monday', '11:00:00', '12:30:00', 'JavaScript Basics', 'Lab 2', 'Rahul Sharma'),
(3, 'Tuesday', '09:00:00', '11:00:00', 'Database Management', 'Room 101', 'Anita Desai'),
(3, 'Wednesday', '14:00:00', '16:00:00', 'PHP Programming', 'Lab 1', 'Amit Patval')";

if (mysqli_query($conn, $sql)) {
    echo "Sample timetables for course_id=3 added.";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
