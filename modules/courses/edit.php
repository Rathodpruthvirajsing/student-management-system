<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$error = '';
$id = $_GET['id'];

$sql = "SELECT * FROM courses WHERE id='$id'";
$result = mysqli_query($conn, $sql);
$course = mysqli_fetch_assoc($result);

if (!$course) {
    header("Location: view.php?msg=Course not found");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = trim($_POST['course_name']);
    $course_code = trim($_POST['course_code']);
    $duration = trim($_POST['duration']);

    if (empty($course_name) || empty($course_code)) {
        $error = "Course name and code are required";
    } else {
        $sql = "UPDATE courses SET course_name='$course_name', course_code='$course_code', duration='$duration' WHERE id='$id'";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: view.php?msg=Course updated successfully");
            exit();
        } else {
            $error = "Error updating course: " . mysqli_error($conn);
        }
    }
}
?>

<div class="content">
    <h2>Edit Course</h2>

    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>

    <form method="POST" class="form-container">
        <div class="form-group">
            <label>Course Name *</label>
            <input type="text" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Course Code *</label>
            <input type="text" name="course_code" value="<?php echo htmlspecialchars($course['course_code']); ?>" required>
        </div>

        <div class="form-group">
            <label>Duration</label>
            <input type="text" name="duration" value="<?php echo htmlspecialchars($course['duration'] ?? ''); ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Update Course</button>
            <a href="view.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
