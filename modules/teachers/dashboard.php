<?php
include "../../auth/session.php";
include "../../config/db.php";

// Ensure role exists and is teacher
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../../index.php?error=Unauthorized+access");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

$user_email = $_SESSION['user_email'];

// Get teacher details
$teacher_query = "SELECT t.*, c.course_name FROM teachers t 
                  LEFT JOIN courses c ON t.course_id = c.id 
                  WHERE t.email = '" . mysqli_real_escape_string($conn, $user_email) . "'";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher = mysqli_fetch_assoc($teacher_result);

if (!$teacher) {
    die("Error: Teacher record not found in 'teachers' table for $user_email.");
}

$teacher_id = $teacher['id'];
$course_id = $teacher['course_id'];

// Get stats for this teacher
$students_count = 0;
if ($course_id) {
    $students_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM students WHERE course_id = '$course_id'");
    $students_row = mysqli_fetch_assoc($students_res);
    $students_count = $students_row['total'];
}

$attendance_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM attendance WHERE marked_by = '$teacher_id' AND attendance_date = CURDATE()");
$attendance_today = mysqli_fetch_assoc($attendance_res)['total'];

$exams_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM exams WHERE course_id = '$course_id'");
$exams_count = mysqli_fetch_assoc($exams_res)['total'];
?>

<div class="content">
    <h2 class="dashboard-title">👨‍🏫 Teacher Dashboard</h2>
    <p>Welcome back, <strong><?php echo htmlspecialchars($teacher['name']); ?></strong>!</p>

    <div class="card-container">
        <div class="card card-blue">
            <div class="card-number"><?php echo $students_count; ?></div>
            <div class="card-label">My Students (<?php echo htmlspecialchars($teacher['course_name'] ?? 'No Course'); ?>)</div>
            <div class="card-icon">👥</div>
        </div>

        <div class="card card-green">
            <div class="card-number"><?php echo $attendance_today; ?></div>
            <div class="card-label">Attendance Marked Today</div>
            <div class="card-icon">📋</div>
        </div>

        <div class="card card-purple">
            <div class="card-number"><?php echo $exams_count; ?></div>
            <div class="card-label">Exams in My Course</div>
            <div class="card-icon">📝</div>
        </div>
    </div>

    <div class="dashboard-section">
        <h3>Quick Teacher Actions</h3>
        <div class="quick-links">
            <a href="../../modules/attendance/mark.php" class="quick-link">Mark Attendance</a>
            <a href="../../modules/exams/create.php" class="quick-link">Manage Exams</a>
            <a href="../../modules/chat/admin.php" class="quick-link">Student Chat</a>
            <a href="../../modules/assignments/view.php" class="quick-link">Manage Assignments</a>
        </div>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
