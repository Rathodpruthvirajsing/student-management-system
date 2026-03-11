<?php
include "../../../auth/session.php";
include "../../../config/db.php";
include "../../../includes/header.php";
include "../../../includes/sidebar.php";

// Fetch all teachers with course names
$sql = "SELECT t.*, c.course_name FROM teachers t LEFT JOIN courses c ON t.course_id = c.id ORDER BY t.name ASC";
$result = mysqli_query($conn, $sql);
$teachers = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>Teachers Management</h2>
        <a href="add.php" class="btn btn-add">+ Add New Teacher</a>
    </div>

    <?php if (isset($_GET['msg'])) { ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php } ?>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Course</th>
                <th>Created Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($teachers) > 0) {
                foreach ($teachers as $teacher) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($teacher['phone'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($teacher['course_name'] ?? 'N/A'); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($teacher['created_at'])); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $teacher['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="delete.php?id=<?php echo $teacher['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this teacher?');">Delete</a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="6" style="text-align:center;">No teachers found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../../../includes/footer.php"; ?>
