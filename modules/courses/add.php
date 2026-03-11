<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = trim($_POST['course_name']);
    $course_code = trim($_POST['course_code']);
    $duration = trim($_POST['duration']);

    if (empty($course_name) || empty($course_code)) {
        $error = "Course name and code are required";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM courses WHERE course_code='$course_code'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Course code already exists";
        } else {
            $sql = "INSERT INTO courses (course_name, course_code, duration) VALUES ('$course_name', '$course_code', '$duration')";
            
            if (mysqli_query($conn, $sql)) {
                header("Location: view.php?msg=Course added successfully");
                exit();
            } else {
                $error = "Error adding course: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="content">
    <h2>Add New Course</h2>
    
    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>

    <form method="POST" class="form-container">
        <div class="form-group">
            <label>Course Name *</label>
            <input type="text" name="course_name" placeholder="e.g., Bachelor of Science" required value="<?php echo isset($_POST['course_name']) ? htmlspecialchars($_POST['course_name']) : ''; ?>">
        </div>

        <div class="form-group">
            <label>Course Code *</label>
            <input type="text" name="course_code" placeholder="e.g., BSC101" required value="<?php echo isset($_POST['course_code']) ? htmlspecialchars($_POST['course_code']) : ''; ?>">
        </div>

        <div class="form-group">
            <label>Duration</label>
            <input type="text" name="duration" placeholder="e.g., 4 Years" value="<?php echo isset($_POST['duration']) ? htmlspecialchars($_POST['duration']) : ''; ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Add Course</button>
            <a href="view.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
