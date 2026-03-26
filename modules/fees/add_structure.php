<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$error = '';

$courses_result = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST['course_id'];
    $total_fee = $_POST['total_fee'];

    if (empty($course_id) || empty($total_fee)) {
        $error = "All fields are required";
    } elseif ($total_fee <= 0) {
        $error = "Warning: Total fee must be a positive number";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM fee_structure WHERE course_id='$course_id'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Fee structure already exists for this course";
        } else {
            $sql = "INSERT INTO fee_structure (course_id, total_fee) VALUES ('$course_id', '$total_fee')";
            
            if (mysqli_query($conn, $sql)) {
                header("Location: structure.php?msg=Fee structure added successfully");
                exit();
            } else {
                $error = "Error adding fee structure: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="content">
    <h2>Add Fee Structure</h2>

    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>

    <form method="POST" class="form-container">
        <div class="form-group">
            <label>Course *</label>
            <select name="course_id" required>
                <option value="">Select Course</option>
                <?php foreach ($courses as $course) { ?>
                    <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Total Fee (Rs.) *</label>
            <input type="number" name="total_fee" step="0.01" placeholder="50000.00" required value="<?php echo isset($_POST['total_fee']) ? htmlspecialchars($_POST['total_fee']) : ''; ?>">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Add Fee Structure</button>
            <a href="structure.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
