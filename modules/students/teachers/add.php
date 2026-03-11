<?php
include "../../../auth/session.php";
include "../../../config/db.php";
include "../../../includes/header.php";
include "../../../includes/sidebar.php";

$error = '';

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
        
        $sql = "INSERT INTO teachers (name, email, phone, course_id) VALUES ('$name', '$email', '$phone', $course_part)";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: view.php?msg=Teacher added successfully");
            exit();
        } else {
            $error = "Error adding teacher: " . mysqli_error($conn);
        }
    }
}
?>

<div class="content">
    <h2>Add New Teacher</h2>

    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>

    <form method="POST" class="form-container">
        <div class="form-group">
            <label>Teacher Name *</label>
            <input type="text" name="name" placeholder="Full Name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="Email Address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="tel" name="phone" placeholder="Phone Number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
        </div>

        <div class="form-group">
            <label>Course</label>
            <select name="course_id">
                <option value="">Select Course</option>
                <?php foreach ($courses as $course) { ?>
                    <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Add Teacher</button>
            <a href="view.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../../includes/footer.php"; ?>
