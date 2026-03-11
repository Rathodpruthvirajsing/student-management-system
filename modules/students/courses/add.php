<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <h2>Add Course</h2>

    <form method="POST">
        <label>Course Name</label><br>
        <input type="text" name="course_name" required><br><br>

        <label>Course Code</label><br>
        <input type="text" name="course_code" required><br><br>

        <label>Duration</label><br>
        <input type="text" name="duration" placeholder="e.g. 3 Years"><br><br>

        <button type="submit" name="save" class="btn btn-add">
            Save Course
        </button>
    </form>
</div>

<?php
if (isset($_POST['save'])) {
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];
    $duration = $_POST['duration'];

    $query = "INSERT INTO courses (course_name, course_code, duration)
              VALUES ('$course_name', '$course_code', '$duration')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Course Added Successfully');</script>";
    } else {
        echo mysqli_error($conn);
    }
}

include "../../includes/footer.php";
?>