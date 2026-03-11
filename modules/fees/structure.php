<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

// Fetch fee structures
$sql = "SELECT fs.*, c.course_name FROM fee_structure fs LEFT JOIN courses c ON fs.course_id = c.id ORDER BY c.course_name ASC";
$result = mysqli_query($conn, $sql);
$structures = mysqli_fetch_all($result, MYSQLI_ASSOC);

$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<div class="content">
    <div class="header-section">
        <h2>Fee Structure</h2>
        <a href="add_structure.php" class="btn btn-add">+ Add Fee Structure</a>
    </div>

    <?php if (isset($_GET['error'])) echo '<div class="alert-error">' . htmlspecialchars($_GET['error']) . '</div>'; ?>
    <?php if (isset($_GET['msg'])) echo '<div class="alert-success">' . htmlspecialchars($_GET['msg']) . '</div>'; ?>

    <table class="table">
        <thead>
            <tr>
                <th>Course</th>
                <th>Total Fee (Rs.)</th>
                <th>Created Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($structures) > 0) {
                foreach ($structures as $structure) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($structure['course_name']); ?></td>
                        <td>Rs. <?php echo number_format($structure['total_fee'], 2); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($structure['created_at'])); ?></td>
                        <td>
                            <a href="edit_structure.php?id=<?php echo $structure['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="delete_structure.php?id=<?php echo $structure['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this structure?');">Delete</a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="4" style="text-align:center;">No fee structures found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
