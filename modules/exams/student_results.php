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

// Get student marks for this student only
$marks_query = "SELECT e.id, e.exam_name, e.total_marks, m.marks_obtained, c.course_name
                FROM marks m 
                JOIN exams e ON m.exam_id = e.id 
                JOIN courses c ON e.course_id = c.id
                WHERE m.student_id='$student_id'
                ORDER BY e.exam_name DESC";
$marks_result = mysqli_query($conn, $marks_query);
$marks = mysqli_fetch_all($marks_result, MYSQLI_ASSOC);

// Calculate average marks
$total_marks_obtained = 0;
$total_marks_possible = 0;
foreach ($marks as $mark) {
    $total_marks_obtained += $mark['marks_obtained'];
    $total_marks_possible += $mark['total_marks'];
}
$overall_percentage = $total_marks_possible > 0 ? round(($total_marks_obtained / $total_marks_possible) * 100, 2) : 0;
?>

<div class="content">
    <div class="header-section">
        <h2>📝 My Exam Results</h2>
    </div>

    <!-- Overall Summary -->
    <?php if (count($marks) > 0): ?>
    <div style="padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; margin-bottom: 25px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <p style="font-size: 12px; opacity: 0.9; margin-bottom: 5px;">OVERALL PERFORMANCE</p>
                <p style="font-size: 32px; font-weight: 600;"><?php echo $overall_percentage; ?>%</p>
            </div>
            <div>
                <p style="font-size: 12px; opacity: 0.9; margin-bottom: 5px;">TOTAL MARKS OBTAINED</p>
                <p style="font-size: 32px; font-weight: 600;"><?php echo $total_marks_obtained; ?> / <?php echo $total_marks_possible; ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Results Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Exam Name</th>
                <th>Course</th>
                <th>Your Marks</th>
                <th>Total Marks</th>
                <th>Percentage</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($marks) > 0): ?>
                <?php foreach ($marks as $mark): 
                    $percentage = ($mark['marks_obtained'] / $mark['total_marks']) * 100;
                    $grade = $percentage >= 90 ? 'A' : ($percentage >= 80 ? 'B' : ($percentage >= 70 ? 'C' : ($percentage >= 60 ? 'D' : 'F')));
                    $grade_color = $grade === 'A' ? '#28a745' : ($grade === 'B' ? '#17a2b8' : ($grade === 'C' ? '#ffc107' : ($grade === 'D' ? '#fd7e14' : '#dc3545')));
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($mark['exam_name']); ?></td>
                        <td><?php echo htmlspecialchars($mark['course_name']); ?></td>
                        <td><?php echo $mark['marks_obtained']; ?></td>
                        <td><?php echo $mark['total_marks']; ?></td>
                        <td><?php echo number_format($percentage, 2); ?>%</td>
                        <td>
                            <span style="background: <?php echo $grade_color; ?>; color: white; padding: 5px 10px; border-radius: 4px; font-weight: 600;">
                                <?php echo $grade; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 30px;">No exam results available yet</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
