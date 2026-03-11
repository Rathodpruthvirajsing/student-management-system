<?php
include "../auth/session.php";
include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

// Generate attendance report
$sql = "SELECT s.enrollment_no, s.name, c.course_name,
        COUNT(CASE WHEN a.status='Present' THEN 1 END) as present,
        COUNT(CASE WHEN a.status='Absent' THEN 1 END) as absent,
        COUNT(*) as total_days,
        ROUND((COUNT(CASE WHEN a.status='Present' THEN 1 END) / COUNT(*)) * 100, 2) as percentage
        FROM students s
        LEFT JOIN courses c ON s.course_id = c.id
        LEFT JOIN attendance a ON s.id = a.student_id
        GROUP BY s.id
        ORDER BY s.name ASC";

$result = mysqli_query($conn, $sql);
$records = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <h2>Attendance Report</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Course</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Total Days</th>
                <th>Attendance %</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $record) { 
                $percentage = $record['total_days'] ? $record['percentage'] : 0;
                $status_class = $percentage >= 75 ? 'pass' : 'fail';
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['name']); ?></td>
                    <td><?php echo htmlspecialchars($record['course_name'] ?? 'N/A'); ?></td>
                    <td><?php echo $record['present']; ?></td>
                    <td><?php echo $record['absent']; ?></td>
                    <td><?php echo $record['total_days']; ?></td>
                    <td><span class="status-<?php echo $status_class; ?>"><?php echo number_format($percentage, 2); ?>%</span></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <button onclick="window.print();" class="btn btn-add" style="margin-top: 20px;">Print Report</button>
</div>

<?php include "../includes/footer.php"; ?>
