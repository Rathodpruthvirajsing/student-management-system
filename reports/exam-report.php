<?php
include "../auth/session.php";
include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

// Generate exam report
$sql = "SELECT e.exam_name, e.total_marks, c.course_name,
        COUNT(DISTINCT m.student_id) as total_students,
        ROUND(AVG(m.marks_obtained), 2) as avg_marks,
        MAX(m.marks_obtained) as max_marks,
        MIN(m.marks_obtained) as min_marks
        FROM exams e
        LEFT JOIN courses c ON e.course_id = c.id
        LEFT JOIN marks m ON e.id = m.exam_id
        GROUP BY e.id
        ORDER BY e.exam_date DESC";

$result = mysqli_query($conn, $sql);
$records = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <h2>Exam Report</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Exam Name</th>
                <th>Course</th>
                <th>Total Marks</th>
                <th>Students</th>
                <th>Average</th>
                <th>Highest</th>
                <th>Lowest</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $record) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['exam_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['course_name']); ?></td>
                    <td><?php echo $record['total_marks']; ?></td>
                    <td><?php echo $record['total_students']; ?></td>
                    <td><?php echo number_format($record['avg_marks'], 2); ?></td>
                    <td><?php echo $record['max_marks']; ?></td>
                    <td><?php echo $record['min_marks']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <button onclick="window.print();" class="btn btn-add" style="margin-top: 20px;">Print Report</button>
</div>

<?php include "../includes/footer.php"; ?>
