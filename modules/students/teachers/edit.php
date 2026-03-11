<?php
include "../../../auth/session.php";
include "../../../config/db.php";
include "../../../includes/header.php";
include "../../../includes/sidebar.php";

$error = '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: view.php?msg=Invalid teacher ID");
    exit();
}

$sql = "SELECT * FROM teachers WHERE id=$id";
$result = mysqli_query($conn, $sql);
$teacher = mysqli_fetch_assoc($result);

if (!$teacher) {
    header("Location: view.php?msg=Teacher not found");
    exit();
}

$courses_result = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $course_id = $_POST['course_id'] ?: 'NULL';

    if (empty($name)) {
        $error = "Teacher name is required";
    } else {
        // Sanitize inputs
        $name = mysqli_real_escape_string($conn, $name);
        $email = mysqli_real_escape_string($conn, $email);
        $phone = mysqli_real_escape_string($conn, $phone);
        $course_part = ($course_id !== 'NULL') ? intval($course_id) : 'NULL';
        
        $sql = "UPDATE teachers SET name='$name', email='$email', phone='$phone', course_id=$course_part WHERE id=$id";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: view.php?msg=Teacher updated successfully");
            exit();
        } else {
            $error = "Error updating teacher: " . mysqli_error($conn);
        }
    }
}
?>

<div class="content">
    <h2>Edit Teacher</h2>

    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>

    <form method="POST" class="form-container">
        <div class="form-group">
            <label>Teacher Name *</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($teacher['name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($teacher['email'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($teacher['phone'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Course</label>
            <select name="course_id">
                <option value="">Select Course</option>
                <?php foreach ($courses as $course) { ?>
                    <option value="<?php echo $course['id']; ?>" <?php echo ($teacher['course_id'] == $course['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($course['course_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Update Teacher</button>
            <a href="view.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../../includes/footer.php"; ?>
