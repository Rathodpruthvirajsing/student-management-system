<?php
include "../../auth/session.php";
include "../../config/db.php";

if ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

// Get teacher's course
$course_id = null;
if ($_SESSION['role'] === 'teacher') {
    $t_res = mysqli_query($conn, "SELECT course_id FROM teachers WHERE email='".mysqli_real_escape_string($conn, $_SESSION['user_email'])."'");
    $course_id = mysqli_fetch_assoc($t_res)['course_id'] ?? null;
}

// Fetch performance data (Average Marks)
$perf_sql = "SELECT s.name, s.enrollment_no, AVG(m.marks_obtained) as avg_marks, 
             (SELECT COUNT(*) FROM attendance a WHERE a.student_id = s.id AND a.status='Present') * 100 / 
             NULLIF((SELECT COUNT(*) FROM attendance a WHERE a.student_id = s.id), 0) as attendance_pct
             FROM students s 
             LEFT JOIN marks m ON s.id = m.student_id";
if ($course_id) {
    $perf_sql .= " WHERE s.course_id = $course_id";
}
$perf_sql .= " GROUP BY s.id ORDER BY avg_marks DESC";
$performance = mysqli_fetch_all(mysqli_query($conn, $perf_sql), MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>📈 Student Performance Analytics</h2>
    </div>

    <div class="card-container" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div class="card" style="background: white; color: #333;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Enrollment No</th>
                        <th>Avg Marks</th>
                        <th>Attendance %</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($performance as $p): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($p['enrollment_no']); ?></td>
                            <td><?php echo number_format($p['avg_marks'], 1); ?></td>
                            <td><?php echo number_format($p['attendance_pct'], 1); ?>%</td>
                            <td>
                                <?php if ($p['avg_marks'] >= 75): ?>
                                    <span style="color: green; font-weight: bold;">Excellent</span>
                                <?php elseif ($p['avg_marks'] >= 50): ?>
                                    <span style="color: orange; font-weight: bold;">Average</span>
                                <?php else: ?>
                                    <span style="color: red; font-weight: bold;">Needs Attention</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
