<?php
include "../../auth/session.php";
include "../../config/db.php";

// Role check
if ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

// If teacher, get their course
$assigned_course_id = null;
if ($_SESSION['role'] === 'teacher') {
    $t_res = mysqli_query($conn, "SELECT course_id FROM teachers WHERE email='".mysqli_real_escape_string($conn, $_SESSION['user_email'])."'");
    $t_data = mysqli_fetch_assoc($t_res);
    $assigned_course_id = $t_data['course_id'] ?? null;
}

$selected_course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : $assigned_course_id;

$courses = mysqli_fetch_all(mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC"), MYSQLI_ASSOC);

$students_sql = "SELECT s.*, c.course_name FROM students s LEFT JOIN courses c ON s.course_id = c.id";
if ($selected_course_id) {
    $students_sql .= " WHERE s.course_id = $selected_course_id";
}
$students_sql .= " ORDER BY s.name ASC";
$students = mysqli_fetch_all(mysqli_query($conn, $students_sql), MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>👨‍🎓 Student List <?php echo $selected_course_id ? "(Course Wise)" : ""; ?></h2>
        
        <form method="GET" class="filter-form" style="display: flex; gap: 10px; align-items: center;">
            <select name="course_id" onchange="this.form.submit()" style="padding: 8px; border-radius: 4px; border: 1px solid #ddd;">
                <option value="">All Courses</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ($selected_course_id == $c['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <noscript><button type="submit">Filter</button></noscript>
        </form>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Enrollment No</th>
                <th>Name</th>
                <th>Course</th>
                <th>Email</th>
                <th>Phone</th>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (count($students) > 0): ?>
                <?php foreach ($students as $s): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($s['enrollment_no']); ?></strong></td>
                        <td><?php echo htmlspecialchars($s['name']); ?></td>
                        <td><span class="badge"><?php echo htmlspecialchars($s['course_name'] ?? 'N/A'); ?></span></td>
                        <td><?php echo htmlspecialchars($s['email']); ?></td>
                        <td><?php echo htmlspecialchars($s['phone']); ?></td>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <td>
                            <a href="../students/edit.php?id=<?php echo $s['id']; ?>" class="btn-secondary"><i class="fas fa-edit"></i></a>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="<?php echo ($_SESSION['role'] === 'admin' ? 6 : 5); ?>" style="text-align:center;">No students found for this selection.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.badge { background: #e0f2fe; color: #075985; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
</style>

<?php include "../../includes/footer.php"; ?>
