<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

$teacher_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Admins see all, Teachers see only their own
if ($role === 'admin') {
    $sql = "SELECT a.*, c.course_name, u.name as teacher_name FROM assignments a 
            LEFT JOIN courses c ON a.course_id = c.id 
            LEFT JOIN users u ON a.created_by = u.id 
            ORDER BY a.created_at DESC";
} else {
    $sql = "SELECT a.*, c.course_name FROM assignments a 
            LEFT JOIN courses c ON a.course_id = c.id 
            WHERE a.created_by = $teacher_id
            ORDER BY a.created_at DESC";
}

$result = mysqli_query($conn, $sql);
$assignments = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>Assignment Management</h2>
        <a href="add.php" class="btn btn-add">+ Add New Assignment</a>
    </div>

    <?php if (isset($_GET['msg'])) echo '<div class="alert-success">'.htmlspecialchars($_GET['msg']).'</div>'; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Course</th>
                <?php if($role === 'admin') echo '<th>Teacher</th>'; ?>
                <th>Due Date</th>
                <th>File</th>
                <th>Submissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($assignments) > 0) {
                foreach ($assignments as $a) { ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($a['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($a['course_name']); ?></td>
                        <?php if($role === 'admin') echo '<td>'.htmlspecialchars($a['teacher_name']??'N/A').'</td>'; ?>
                        <td><?php echo date('d-M-Y', strtotime($a['due_date'])); ?></td>
                        <td><a href="../../<?php echo $a['file_path']; ?>" target="_blank" class="btn btn-edit" title="View File" style="background:#6c757d;"><i class="fa-solid fa-file-pdf"></i></a></td>
                        <td>
                            <a href="submissions.php?assignment_id=<?php echo $a['id']; ?>" class="btn btn-edit" title="View Submissions" style="background:#8e44ad; width:auto; padding: 0 10px;">
                                <i class="fa-solid fa-users"></i> View
                            </a>
                        </td>
                        <td>
                            <a href="edit.php?id=<?php echo $a['id']; ?>" class="btn btn-edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="delete.php?id=<?php echo $a['id']; ?>" class="btn btn-delete" title="Delete" onclick="return confirm('Delete this assignment?');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="7" style="text-align:center;">No assignments posted yet</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
