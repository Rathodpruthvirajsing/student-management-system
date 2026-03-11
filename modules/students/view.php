<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

// Fetch all students with course names
$sql = "SELECT s.*, c.course_name FROM students s LEFT JOIN courses c ON s.course_id = c.id ORDER BY s.name ASC";
$result = mysqli_query($conn, $sql);
$students = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>Students Management</h2>
        <a href="add.php" class="btn btn-add">+ Add New Student</a>
    </div>

    <?php if (isset($_GET['msg'])) { ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php } ?>

    <table class="table">
        <thead>
            <tr>
                <th>Enrollment No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Course</th>
                <th>DOB</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($students) > 0) {
                foreach ($students as $student) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['enrollment_no']); ?></td>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($student['gender'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($student['course_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($student['dob'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="delete.php?id=<?php echo $student['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this student?');">Delete</a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="8" style="text-align:center;">No students found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>