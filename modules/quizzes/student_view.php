<?php
include "../../auth/session.php";
include "../../config/db.php";

if ($_SESSION['role'] !== 'student') {
    header("Location: ../../home.php?error=Unauthorized+access");
    exit();
}

$student_id = $_SESSION['user_id'];

// Get the student's courses to check which quizzes they have access to
$student_course_res = mysqli_query($conn, "SELECT course_id FROM students WHERE email = (SELECT email FROM users WHERE id = $student_id)");
$course_row = mysqli_fetch_assoc($student_course_res);
$course_id = $course_row ? intval($course_row['course_id']) : 0;

include "../../includes/header.php";
include "../../includes/sidebar.php";

if ($course_id > 0) {
    // Fetch active quizzes for this course
    $sql = "SELECT q.*, s.subject_name, t.name as teacher_name, 
            (SELECT score FROM quiz_attempts qa WHERE qa.quiz_id = q.id AND qa.student_id = $student_id) as my_score
            FROM quizzes q
            JOIN subjects s ON q.subject_id = s.id
            JOIN users t ON q.teacher_id = t.id
            WHERE q.course_id = $course_id AND q.is_active = 1
            ORDER BY q.quiz_date DESC, q.start_time DESC";
    $result = mysqli_query($conn, $sql);
}

$current_date = date('Y-m-d');
$current_time = date('H:i:s');
?>

<div class="content">
    <div class="header-section">
        <h2 class="dashboard-title">Online Quiz Portal</h2>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <?php if ($course_id == 0): ?>
        <div class="alert-error">You are not enrolled in any course. Please contact administrator.</div>
    <?php else: ?>
        <div class="card-container" style="display: block;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Quiz Title</th>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Date & Time</th>
                        <th>Time Limit</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <?php 
                                $quiz_date = $row['quiz_date'];
                                $start_time = $row['start_time'];
                                $end_time = $row['end_time'];
                                
                                $is_future = ($quiz_date > $current_date) || ($quiz_date == $current_date && $start_time > $current_time);
                                $is_active = ($quiz_date == $current_date && $current_time >= $start_time && $current_time <= $end_time);
                                $is_past = ($quiz_date < $current_date) || ($quiz_date == $current_date && $current_time > $end_time);
                                $has_attempted = isset($row['my_score']);
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                                <td>
                                    <?php echo date('d M Y', strtotime($quiz_date)); ?><br>
                                    <small><?php echo date('h:i A', strtotime($start_time)) . ' - ' . date('h:i A', strtotime($end_time)); ?></small>
                                </td>
                                <td><?php echo $row['time_limit']; ?> mins</td>
                                <td>
                                    <?php if ($has_attempted): ?>
                                        <span class="status-present" style="background:#d4edda; color:#155724;">Score: <?php echo $row['my_score']; ?></span>
                                    <?php elseif ($is_past): ?>
                                        <span class="status-absent" style="background:#f8d7da; color:#721c24;">Expired</span>
                                    <?php elseif ($is_active): ?>
                                        <span class="status-present" style="background:#d1ecf1; color:#0c5460;">Live Now</span>
                                    <?php else: ?>
                                        <span class="status-pending" style="background:#fff3cd; color:#856404;">Upcoming</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($has_attempted): ?>
                                        <button class="btn btn-cancel" disabled style="opacity: 0.5;">Completed</button>
                                    <?php elseif ($is_active): ?>
                                        <a href="take.php?id=<?php echo $row['id']; ?>" class="btn btn-add" style="background:#2ecc71;">Launch Quiz</a>
                                    <?php elseif ($is_future): ?>
                                        <button class="btn btn-cancel" disabled style="opacity: 0.5;">Not Started</button>
                                    <?php else: ?>
                                        <button class="btn btn-cancel" disabled style="opacity: 0.5;">Missed</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No active quizzes found for your course.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include "../../includes/footer.php"; ?>
