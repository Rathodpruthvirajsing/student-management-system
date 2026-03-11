<?php
include "../auth/session.php";
include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

// Generate student report
$sql = "SELECT s.*, c.course_name, 
        (SELECT COUNT(*) FROM attendance WHERE student_id=s.id AND status='Present') as present_count,
        (SELECT COUNT(*) FROM attendance WHERE student_id=s.id AND status='Absent') as absent_count,
        (SELECT COALESCE(AVG(marks_obtained), 0) FROM marks m 
         INNER JOIN exams e ON m.exam_id=e.id 
         WHERE m.student_id=s.id) as avg_marks
        FROM students s 
        LEFT JOIN courses c ON s.course_id=c.id
        ORDER BY s.name ASC";

$result = mysqli_query($conn, $sql);
$students = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <h2>Student Report</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Enrollment No</th>
                <th>Name</th>
                <th>Course</th>
                <th>Email</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Avg Marks</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['enrollment_no']); ?></td>
                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                    <td><?php echo htmlspecialchars($student['course_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></td>
                    <td><?php echo $student['present_count']; ?></td>
                    <td><?php echo $student['absent_count']; ?></td>
                    <td><?php echo number_format($student['avg_marks'], 2); ?></td>
                    <td>
                        <a href="javascript:window.print();" class="btn btn-edit">Print</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <button onclick="window.print();" class="btn btn-add" style="margin-top: 20px;">Print Report</button>
</div>

<?php include "../includes/footer.php"; ?>
