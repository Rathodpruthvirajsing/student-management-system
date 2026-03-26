<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$sql = "SELECT s.*, c.course_name FROM subjects s LEFT JOIN courses c ON s.course_id = c.id ORDER BY s.subject_name ASC";
$subjects = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>📚 Subjects Management</h2>
        <a href="add.php" class="btn btn-add">+ Add New Subject</a>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Subject Name</th>
                <th>Code</th>
                <th>Course</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($subjects) > 0): ?>
                <?php foreach ($subjects as $s): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($s['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($s['subject_code']); ?></td>
                        <td><?php echo htmlspecialchars($s['course_name'] ?? 'N/A'); ?></td>
                        <td><?php echo date('d M Y', strtotime($s['created_at'])); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $s['id']; ?>" class="btn-secondary"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?php echo $s['id']; ?>" class="btn-delete" onclick="return confirm('Delete subject?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">No subjects found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
