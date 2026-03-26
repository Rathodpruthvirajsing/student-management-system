<?php
include "../config/db.php";

if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    $query = "SELECT id, subject_name FROM subjects WHERE course_id = $course_id ORDER BY subject_name";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<option value="">-- Choose Subject --</option>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['subject_name']) . '</option>';
        }
    } else {
        echo '<option value="">-- No Subjects Found --</option>';
    }
}
?>
