<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$error = '';

// Fetch students and courses
$students_result = mysqli_query($conn, "SELECT id, name, enrollment_no FROM students ORDER BY name ASC");
$students = mysqli_fetch_all($students_result, MYSQLI_ASSOC);

$courses_result = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];
    
    // Correctly handle marked_by based on role
    $marked_by = "NULL";
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'teacher') {
        $user_id = $_SESSION['user_id'];
        $teacher_query = "SELECT id FROM teachers WHERE email = (SELECT email FROM users WHERE id='$user_id')";
        $teacher_result = mysqli_query($conn, $teacher_query);
        if ($teacher_row = mysqli_fetch_assoc($teacher_result)) {
            $marked_by = "'" . $teacher_row['id'] . "'";
        }
    }

    if (empty($student_id) || empty($course_id) || empty($attendance_date)) {
        $error = "All fields are required";
    } else {
        $sql = "INSERT INTO attendance (student_id, course_id, attendance_date, status, marked_by) 
                VALUES ('$student_id', '$course_id', '$attendance_date', '$status', $marked_by)";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: view.php?msg=Attendance marked successfully");
            exit();
        } else {
            $error = "Error marking attendance: " . mysqli_error($conn);
        }
    }
}
?>

<div class="content">
    <h2>Mark Attendance</h2>

    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>

    <form method="POST" class="form-container">
        <div class="form-group">
            <label>Student *</label>
            <select name="student_id" required>
                <option value="">Select Student</option>
                <?php foreach ($students as $student) { ?>
                    <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['name'] . ' (' . $student['enrollment_no'] . ')'); ?></option>
                <?php } ?>
            </select>
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
                <label>Date *</label>
                <input type="date" name="attendance_date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label>Status *</label>
                <select name="status" required>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Mark Attendance</button>
            <a href="view.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
