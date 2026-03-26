<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

$assignment_id = isset($_GET['assignment_id']) ? intval($_GET['assignment_id']) : 0;

// Get assignment info
$a_res = mysqli_query($conn, "SELECT * FROM assignments WHERE id = $assignment_id");
$assignment = mysqli_fetch_assoc($a_res);

// Get all submissions for this assignment
$sql = "SELECT s.*, u.name as student_name, u.email as student_email, st.enrollment_no 
        FROM assignment_submissions s
        JOIN users u ON s.student_id = u.id
        LEFT JOIN students st ON u.email = st.email
        WHERE s.assignment_id = $assignment_id
        ORDER BY s.submitted_at DESC";
$result = mysqli_query($conn, $sql);
$submissions = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>Submissions: <?php echo htmlspecialchars($assignment['title'] ?? 'Unknown'); ?></h2>
        <a href="view.php" class="btn btn-cancel">Back to Assignments</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Enrollment No</th>
                <th>Submission Date</th>
                <th>File</th>
                <th>Remarks</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($submissions) > 0) {
                foreach ($submissions as $s) { ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($s['student_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($s['enrollment_no'] ?? 'N/A'); ?></td>
                        <td><?php echo date('d-M-Y H:i', strtotime($s['submitted_at'])); ?></td>
                        <td>
                            <a href="../../<?php echo $s['file_path']; ?>" target="_blank" class="btn btn-add" style="background:#28a745; width:auto; padding: 0 10px;">
                                <i class="fa-solid fa-download"></i> Download
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($s['teacher_remarks'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($s['grade'] ?? 'Not Graded'); ?></td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="6" style="text-align:center;">No submissions yet.</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
