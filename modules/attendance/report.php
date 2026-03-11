<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

// Fetch attendance report by student
$student_id = isset($_GET['student_id']) ? $_GET['student_id'] : '';

$sql = "SELECT a.*, s.name as student_name, s.enrollment_no, c.course_name 
        FROM attendance a 
        LEFT JOIN students s ON a.student_id = s.id 
        LEFT JOIN courses c ON a.course_id = c.id ";

if ($student_id) {
    $sql .= "WHERE a.student_id='$student_id' ";
}
$sql .= "ORDER BY a.attendance_date DESC";

$result = mysqli_query($conn, $sql);
$attendance = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get students for filter
$students_result = mysqli_query($conn, "SELECT id, name, enrollment_no FROM students ORDER BY name ASC");
$students = mysqli_fetch_all($students_result, MYSQLI_ASSOC);
?>

<div class="content">
    <h2>Attendance Report</h2>

    <form method="GET" style="margin-bottom: 20px;">
        <div class="form-row">
            <div class="form-group">
                <label>Filter by Student:</label>
                <select name="student_id" onchange="this.form.submit();">
                    <option value="">All Students</option>
                    <?php foreach ($students as $student) { ?>
                        <option value="<?php echo $student['id']; ?>" <?php echo ($student_id == $student['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($student['name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Enrollment No</th>
                <th>Course</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($attendance) > 0) {
                foreach ($attendance as $record) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['enrollment_no']); ?></td>
                        <td><?php echo htmlspecialchars($record['course_name']); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($record['attendance_date'])); ?></td>
                        <td><span class="status-<?php echo strtolower($record['status']); ?>"><?php echo $record['status']; ?></span></td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="5" style="text-align:center;">No attendance records found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
