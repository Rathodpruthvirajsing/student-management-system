<?php
include "../../auth/session.php";
include "../../config/db.php";

// Allow both student and parent roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['student', 'parent'])) {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

$user_id = $_SESSION['user_id'];
$course_id = 0;
$student_id_for_submission = $user_id; // Used for submission check
$is_parent = ($_SESSION['role'] === 'parent');

if ($_SESSION['role'] === 'student') {
    // Get student course ID
    $student_query = "SELECT id, course_id FROM students WHERE email = (SELECT email FROM users WHERE id=$user_id)";
    $student_res = mysqli_query($conn, $student_query);
    $student = mysqli_fetch_assoc($student_res);
    $course_id = $student['course_id'] ?? 0;
    $student_id_for_submission = $student['id'] ?? $user_id;
} elseif ($_SESSION['role'] === 'parent') {
    // Get child's course via parents table
    $user_email = $_SESSION['user_email'];
    $parent_query = "SELECT p.student_id, s.course_id FROM parents p 
                     LEFT JOIN students s ON p.student_id = s.id 
                     WHERE p.email = '" . mysqli_real_escape_string($conn, $user_email) . "'";
    $parent_res = mysqli_query($conn, $parent_query);
    $parent = mysqli_fetch_assoc($parent_res);
    if (!$parent || !$parent['student_id']) {
        echo "<div class='content'><h2>Error</h2><p>No linked student found for this parent account.</p></div>";
        include "../../includes/footer.php";
        exit();
    }
    $course_id = $parent['course_id'] ?? 0;
    $student_id_for_submission = $parent['student_id'];
}

// Fetch assignments for this course and check if student already submitted
$sql = "SELECT a.*, (SELECT id FROM assignment_submissions WHERE assignment_id = a.id AND student_id = $student_id_for_submission) as submission_id 
        FROM assignments a 
        WHERE a.course_id = $course_id 
        ORDER BY a.due_date ASC";
$result = mysqli_query($conn, $sql);
$assignments = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>📂 <?php echo $is_parent ? "Child's Assignments" : "Assignments & Learning Resources"; ?></h2>
    </div>

    <?php if (isset($_GET['msg'])) echo '<div class="alert-success">'.htmlspecialchars($_GET['msg']).'</div>'; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
        <?php if (count($assignments) > 0) {
            foreach ($assignments as $a) { 
                $is_expired = strtotime($a['due_date']) < strtotime(date('Y-m-d'));
                $is_submitted = !empty($a['submission_id']);
                ?>
                <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-top: 4px solid <?php echo $is_submitted ? '#28a745' : ($is_expired ? '#dc3545' : '#1abc9c'); ?>">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                        <h3 style="margin: 0; font-size: 18px;"><?php echo htmlspecialchars($a['title']); ?></h3>
                        <?php if($is_submitted): ?>
                            <span style="background: #d4edda; color: #155724; font-size: 10px; padding: 3px 8px; border-radius: 10px; font-weight: bold;">SUBMITTED</span>
                        <?php elseif($is_expired): ?>
                            <span style="background: #f8d7da; color: #721c24; font-size: 10px; padding: 3px 8px; border-radius: 10px; font-weight: bold;">EXPIRED</span>
                        <?php endif; ?>
                    </div>
                    
                    <p style="color: #666; font-size: 13px; margin-bottom: 15px; min-height: 40px;">
                        <?php echo htmlspecialchars($a['description'] ?: 'No description provided.'); ?>
                    </p>

                    <div style="font-size: 12px; color: #333; margin-bottom: 20px;">
                        <strong>📅 Due Date:</strong> <?php echo date('d-M-Y', strtotime($a['due_date'])); ?>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <a href="../../<?php echo $a['file_path']; ?>" target="_blank" class="btn btn-add" style="flex: 1; font-size: 12px; background: #6c757d;">
                             <i class="fa-solid fa-download"></i> Download Task
                        </a>
                        
                        <?php if(!$is_parent && !$is_submitted && !$is_expired): ?>
                            <a href="submit.php?id=<?php echo $a['id']; ?>" class="btn btn-add" style="flex: 1; font-size: 12px;">
                                <i class="fa-solid fa-upload"></i> Submit Work
                            </a>
                        <?php elseif($is_submitted): ?>
                             <div class="btn" style="flex: 1; background: #e9ecef; color: #7f8c8d; text-align: center; cursor: default;">Submitted</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php }
        } else { ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #aaa; background: white; border-radius: 8px;">
                No assignments found for your course.
            </div>
        <?php } ?>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
