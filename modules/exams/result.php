<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$exam_id = $_GET['exam_id'];

$sql = "SELECT e.*, c.course_name, s.name as student_name, s.enrollment_no, m.marks_obtained
        FROM marks m
        INNER JOIN exams e ON m.exam_id = e.id
        INNER JOIN students s ON m.student_id = s.id
        LEFT JOIN courses c ON e.course_id = c.id
        WHERE e.id='$exam_id'
        ORDER BY m.marks_obtained DESC";

$result = mysqli_query($conn, $sql);
$results = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get exam details
$exam_result = mysqli_query($conn, "SELECT * FROM exams WHERE id='$exam_id'");
$exam = mysqli_fetch_assoc($exam_result);
?>

<div class="content">
    <h2>Exam Results - <?php echo htmlspecialchars($exam['exam_name']); ?></h2>
    <p><strong>Course:</strong> <?php echo htmlspecialchars($exam['course_name'] ?? 'N/A'); ?> | 
       <strong>Total Marks:</strong> <?php echo $exam['total_marks']; ?> |
       <strong>Exam Date:</strong> <?php echo $exam['exam_date'] ? date('d-M-Y', strtotime($exam['exam_date'])) : 'N/A'; ?>
    </p>

    <table class="table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Student Name</th>
                <th>Enrollment No</th>
                <th>Marks Obtained</th>
                <th>Percentage</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $rank = 1;
            foreach ($results as $result) { 
                $percentage = ($result['marks_obtained'] / $exam['total_marks']) * 100;
                if ($percentage >= 90) $grade = 'A';
                elseif ($percentage >= 80) $grade = 'B';
                elseif ($percentage >= 70) $grade = 'C';
                elseif ($percentage >= 60) $grade = 'D';
                else $grade = 'F';
            ?>
                <tr>
                    <td><?php echo $rank; ?></td>
                    <td><?php echo htmlspecialchars($result['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($result['enrollment_no']); ?></td>
                    <td><?php echo $result['marks_obtained']; ?></td>
                    <td><?php echo number_format($percentage, 2); ?>%</td>
                    <td><strong><?php echo $grade; ?></strong></td>
                </tr>
            <?php $rank++; } ?>
        </tbody>
    </table>

    <a href="create.php" class="btn btn-cancel">Back to Exams</a>
</div>

<?php include "../../includes/footer.php"; ?>
