<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$error = '';
$id = $_GET['id'];

$sql = "SELECT fs.*, c.course_name FROM fee_structure fs LEFT JOIN courses c ON fs.course_id = c.id WHERE fs.id='$id'";
$result = mysqli_query($conn, $sql);
$structure = mysqli_fetch_assoc($result);

if (!$structure) {
    header("Location: structure.php?error=Fee structure not found");
    exit();
}

$courses_result = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $total_fee = $_POST['total_fee'];

    if (empty($total_fee)) {
        $error = "Total fee is required";
    } else {
        $sql = "UPDATE fee_structure SET total_fee='$total_fee' WHERE id='$id'";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: structure.php?msg=Fee structure updated successfully");
            exit();
        } else {
            $error = "Error updating fee structure: " . mysqli_error($conn);
        }
    }
}
?>

<div class="content">
    <h2>Edit Fee Structure</h2>

    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>

    <form method="POST" class="form-container">
        <div class="form-group">
            <label>Course</label>
            <input type="text" value="<?php echo htmlspecialchars($structure['course_name']); ?>" disabled>
        </div>

        <div class="form-group">
            <label>Total Fee (Rs.) *</label>
            <input type="number" name="total_fee" step="0.01" value="<?php echo htmlspecialchars($structure['total_fee']); ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Update Fee Structure</button>
            <a href="structure.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
