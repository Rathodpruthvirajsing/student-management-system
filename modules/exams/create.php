<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

// Fetch all exams with course names
$sql = "SELECT e.*, c.course_name FROM exams e LEFT JOIN courses c ON e.course_id = c.id ORDER BY e.exam_date DESC";
$result = mysqli_query($conn, $sql);
$exams = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>Exams Management</h2>
        <a href="add_exam.php" class="btn btn-add">+ Create New Exam</a>
    </div>

    <?php if (isset($_GET['msg'])) { ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php } ?>

    <table class="table">
        <thead>
            <tr>
                <th>Exam Name</th>
                <th>Course</th>
                <th>Exam Date</th>
                <th>Total Marks</th>
                <th>Created Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($exams) > 0) {
                foreach ($exams as $exam) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($exam['exam_name']); ?></td>
                        <td><?php echo htmlspecialchars($exam['course_name']); ?></td>
                        <td><?php echo $exam['exam_date'] ? date('d-M-Y', strtotime($exam['exam_date'])) : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($exam['total_marks']); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($exam['created_at'])); ?></td>
                        <td>
                            <a href="marks.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-edit">Add Marks</a>
                            <a href="result.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-edit">View Results</a>
                            <a href="delete.php?id=<?php echo $exam['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this exam?');">Delete</a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="6" style="text-align:center;">No exams found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
