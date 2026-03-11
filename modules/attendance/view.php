<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

// Fetch attendance records with related data
$sql = "SELECT a.*, s.name as student_name, s.enrollment_no, c.course_name, t.name as teacher_name 
        FROM attendance a 
        LEFT JOIN students s ON a.student_id = s.id 
        LEFT JOIN courses c ON a.course_id = c.id 
        LEFT JOIN teachers t ON a.marked_by = t.id 
        ORDER BY a.attendance_date DESC";
$result = mysqli_query($conn, $sql);
$attendance = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>Attendance Management</h2>
        <a href="mark.php" class="btn btn-add">+ Mark Attendance</a>
    </div>

    <?php if (isset($_GET['msg'])) { ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php } ?>

    <table class="table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Enrollment No</th>
                <th>Course</th>
                <th>Date</th>
                <th>Status</th>
                <th>Marked By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($attendance) > 0) {
                foreach ($attendance as $record) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($record['enrollment_no']); ?></td>
                        <td><?php echo htmlspecialchars($record['course_name'] ?? 'N/A'); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($record['attendance_date'])); ?></td>
                        <td><span class="status-<?php echo strtolower($record['status']); ?>"><?php echo $record['status']; ?></span></td>
                        <td><?php echo htmlspecialchars($record['teacher_name'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="delete.php?id=<?php echo $record['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this record?');">Delete</a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="7" style="text-align:center;">No attendance records found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
