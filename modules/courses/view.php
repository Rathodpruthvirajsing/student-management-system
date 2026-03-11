<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

// Fetch all courses
$sql = "SELECT * FROM courses ORDER BY course_name ASC";
$result = mysqli_query($conn, $sql);
$courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>Courses Management</h2>
        <a href="add.php" class="btn btn-add">+ Add New Course</a>
    </div>

    <?php if (isset($_GET['msg'])) { ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php } ?>

    <table class="table">
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Course Code</th>
                <th>Duration</th>
                <th>Created Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($courses) > 0) {
                foreach ($courses as $course) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                        <td><?php echo htmlspecialchars($course['duration'] ?? 'N/A'); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($course['created_at'])); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $course['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="delete.php?id=<?php echo $course['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this course?');">Delete</a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="5" style="text-align:center;">No courses found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
