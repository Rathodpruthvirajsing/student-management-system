<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!in_array($_SESSION['role'], ['admin', 'teacher'])) {
    header("Location: ../../home.php?error=Unauthorized+access");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: teacher_view.php");
    exit();
}

$quiz_id = intval($_GET['id']);

$sql = "SELECT qa.*, s.name, s.enrollment_no 
        FROM quiz_attempts qa
        JOIN users u ON qa.student_id = u.id
        JOIN students s ON u.email = s.email
        WHERE qa.quiz_id = $quiz_id
        ORDER BY qa.score DESC, qa.completed_at ASC";
$result = mysqli_query($conn, $sql);

$quiz_sql = "SELECT title FROM quizzes WHERE id = $quiz_id";
$quiz_res = mysqli_query($conn, $quiz_sql);
$quiz_title = mysqli_fetch_assoc($quiz_res)['title'];

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <div class="header-section">
        <h2 class="dashboard-title"><i class="fas fa-poll"></i> Results: <?php echo htmlspecialchars($quiz_title); ?></h2>
        <a href="teacher_view.php" class="btn btn-cancel">Back to Quizzes</a>
    </div>

    <div class="card-container" style="display: block;">
        <table class="table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Enrollment No.</th>
                    <th>Score</th>
                    <th>Total Questions</th>
                    <th>Percentage</th>
                    <th>Completed At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): 
                        $percentage = ($row['score'] / $row['total_questions']) * 100;
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['enrollment_no']); ?></td>
                            <td><?php echo $row['score']; ?></td>
                            <td><?php echo $row['total_questions']; ?></td>
                            <td>
                                <?php if ($percentage >= 75): ?>
                                    <span class="status-present" style="background:#d4edda; color:#155724;"><?php echo round($percentage, 2); ?>% (Excellent)</span>
                                <?php elseif ($percentage >= 50): ?>
                                    <span class="status-pending" style="background:#fff3cd; color:#856404;"><?php echo round($percentage, 2); ?>% (Pass)</span>
                                <?php else: ?>
                                    <span class="status-absent" style="background:#f8d7da; color:#721c24;"><?php echo round($percentage, 2); ?>% (Needs Review)</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d M Y, h:i A', strtotime($row['completed_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No students have taken this exam yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
