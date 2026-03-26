<?php
include "../../auth/session.php";
include "../../config/db.php";

// Allow Admin & Teacher
if (!in_array($_SESSION['role'], ['admin', 'teacher'])) {
    header("Location: ../../home.php?error=Unauthorized+access");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch quizzes
if ($role === 'admin') {
    $sql = "SELECT q.*, c.course_name, s.subject_name, t.name as teacher_name 
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            JOIN subjects s ON q.subject_id = s.id
            JOIN users t ON q.teacher_id = t.id
            ORDER BY q.quiz_date DESC, q.start_time DESC";
} else {
    $sql = "SELECT q.*, c.course_name, s.subject_name, t.name as teacher_name 
            FROM quizzes q
            JOIN courses c ON q.course_id = c.id
            JOIN subjects s ON q.subject_id = s.id
            JOIN users t ON q.teacher_id = t.id
            WHERE q.teacher_id = $user_id
            ORDER BY q.quiz_date DESC, q.start_time DESC";
}
$result = mysqli_query($conn, $sql);
?>

<div class="content">
    <div class="header-section">
        <h2 class="dashboard-title">Manage Online Quizzes</h2>
        <?php if ($role === 'teacher'): ?>
            <a href="create.php" class="btn btn-add"><i class="fas fa-plus"></i> Create New AI Quiz</a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <div class="card-container" style="display: block;">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Course & Subject</th>
                    <?php if ($role === 'admin') echo "<th>Created By</th>"; ?>
                    <th>Date</th>
                    <th>Time Limit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['course_name'] . ' - ' . $row['subject_name']); ?></td>
                        <?php if ($role === 'admin') echo "<td>" . htmlspecialchars($row['teacher_name']) . "</td>"; ?>
                        <td><?php echo date('d M Y', strtotime($row['quiz_date'])) . ' (' . date('h:i A', strtotime($row['start_time'])) . ' - ' . date('h:i A', strtotime($row['end_time'])) . ')'; ?></td>
                        <td><?php echo $row['time_limit']; ?> mins</td>
                        <td>
                            <?php if ($row['is_active']): ?>
                                <span class="status-present" style="background:#d4edda; color:#155724;">Active</span>
                            <?php else: ?>
                                <span class="status-absent" style="background:#f8d7da; color:#721c24;">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="view_attempts.php?id=<?php echo $row['id']; ?>" class="btn btn-edit" title="View Attempts" style="background-color: #f1c40f;"><i class="fas fa-eye"></i></a>
                            <?php if ($role === 'teacher'): ?>
                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" title="Delete Quiz" onclick="return confirm('Are you sure you want to delete this quiz? All records will be removed.');"><i class="fas fa-trash-alt"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr>
                        <td colspan="<?php echo ($role === 'admin') ? '7' : '6'; ?>" style="text-align: center;">No quizzes found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
