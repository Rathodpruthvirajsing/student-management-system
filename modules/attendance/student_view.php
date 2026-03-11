<?php
include "../../auth/session.php";
include "../../config/db.php";

// Check if student
if ($_SESSION['role'] !== 'student') {
    header("Location: ../../dashboard.php");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

// Get student info
$user_id = $_SESSION['user_id'];
$student_query = "SELECT id, email, name FROM students WHERE email = (SELECT email FROM users WHERE id='$user_id')";
$student_result = mysqli_query($conn, $student_query);
$student = mysqli_fetch_assoc($student_result);

// Check if student exists
if (!$student) {
    header("Location: ../../auth/logout.php");
    exit();
}

$student_id = $student['id'];

// Get attendance records for this student only
$sql = "SELECT a.id, a.attendance_date, a.status, c.course_name 
        FROM attendance a 
        JOIN courses c ON a.course_id = c.id 
        WHERE a.student_id='$student_id'
        ORDER BY a.attendance_date DESC";
$result = mysqli_query($conn, $sql);
$attendance_records = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Calculate stats
$total_classes = count($attendance_records);
$present_count = 0;
foreach ($attendance_records as $record) {
    if ($record['status'] === 'Present') {
        $present_count++;
    }
}
$attendance_percentage = $total_classes > 0 ? round(($present_count / $total_classes) * 100, 2) : 0;
?>

<div class="content">
    <div class="header-section">
        <h2>📋 My Attendance</h2>
    </div>

    <!-- Attendance Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <div style="padding: 15px; background: #f0f8ff; border-radius: 8px; border-left: 4px solid #2196F3; text-align: center;">
            <div style="font-size: 24px; font-weight: 600; color: #2196F3;"><?php echo $total_classes; ?></div>
            <div style="font-size: 12px; color: #666;">Total Classes</div>
        </div>
        <div style="padding: 15px; background: #f0fff4; border-radius: 8px; border-left: 4px solid #28a745; text-align: center;">
            <div style="font-size: 24px; font-weight: 600; color: #28a745;"><?php echo $present_count; ?></div>
            <div style="font-size: 12px; color: #666;">Days Present</div>
        </div>
        <div style="padding: 15px; background: #fff5f5; border-radius: 8px; border-left: 4px solid #dc3545; text-align: center;">
            <div style="font-size: 24px; font-weight: 600; color: #dc3545;"><?php echo $total_classes - $present_count; ?></div>
            <div style="font-size: 12px; color: #666;">Days Absent</div>
        </div>
        <div style="padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107; text-align: center;">
            <div style="font-size: 24px; font-weight: 600; color: #ffc107;"><?php echo $attendance_percentage; ?>%</div>
            <div style="font-size: 12px; color: #666;">Attendance %</div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Course</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($attendance_records) > 0): ?>
                <?php foreach ($attendance_records as $record): ?>
                    <tr>
                        <td><?php echo date('d-M-Y', strtotime($record['attendance_date'])); ?></td>
                        <td><?php echo htmlspecialchars($record['course_name']); ?></td>
                        <td>
                            <span style="padding: 5px 10px; border-radius: 4px; font-weight: 600; 
                                <?php echo $record['status'] === 'Present' ? 'background: #d4edda; color: #155724;' : 'background: #f8d7da; color: #721c24;'; ?>">
                                <?php echo $record['status']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 30px;">No attendance records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
