<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$sql = "SELECT ta.*, t.name as teacher_name FROM teacher_attendance ta JOIN teachers t ON ta.teacher_id = t.id ORDER BY ta.attendance_date DESC";
$attendance = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>📑 Teacher Attendance</h2>
        <a href="mark_teacher.php" class="btn btn-add">+ Mark Teacher Attendance</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Teacher Name</th>
                <th>Date</th>
                <th>Status</th>
                <th>Marked At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($attendance) > 0): ?>
                <?php foreach ($attendance as $a): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['teacher_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($a['attendance_date'])); ?></td>
                        <td><span class="status-<?php echo strtolower($a['status']); ?>"><?php echo $a['status']; ?></span></td>
                        <td><?php echo $a['marked_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center;">No records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
