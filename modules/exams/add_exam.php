<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$error = '';

$courses_result = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $exam_name = trim($_POST['exam_name']);
    $course_id = $_POST['course_id'];
    $exam_date = $_POST['exam_date'];
    $total_marks = $_POST['total_marks'];

    if (empty($exam_name) || empty($course_id) || empty($total_marks)) {
        $error = "Exam name, course, and total marks are required";
    } else {
        $sql = "INSERT INTO exams (exam_name, course_id, exam_date, total_marks) VALUES ('$exam_name', '$course_id', '$exam_date', '$total_marks')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: create.php?msg=Exam created successfully");
            exit();
        } else {
            $error = "Error creating exam: " . mysqli_error($conn);
        }
    }
}
?>

<div class="content">
    <h2>Create New Exam</h2>

    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>

    <form method="POST" class="form-container">
        <div class="form-group">
            <label>Exam Name *</label>
            <input type="text" name="exam_name" placeholder="e.g., Mid Semester Exam" required value="<?php echo isset($_POST['exam_name']) ? htmlspecialchars($_POST['exam_name']) : ''; ?>">
        </div>

        <div class="form-group">
            <label>Course *</label>
            <select name="course_id" required>
                <option value="">Select Course</option>
                <?php foreach ($courses as $course) { ?>
                    <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Exam Date</label>
                <input type="date" name="exam_date" value="<?php echo isset($_POST['exam_date']) ? htmlspecialchars($_POST['exam_date']) : ''; ?>">
            </div>

            <div class="form-group">
                <label>Total Marks *</label>
                <input type="number" name="total_marks" placeholder="100" required value="<?php echo isset($_POST['total_marks']) ? htmlspecialchars($_POST['total_marks']) : ''; ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Create Exam</button>
            <a href="create.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
