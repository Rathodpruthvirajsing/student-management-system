<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$error = '';
$id = $_GET['id'];

// Fetch student details
$sql = "SELECT * FROM students WHERE id='$id'";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    header("Location: view.php?msg=Student not found");
    exit();
}

// Fetch all courses
$courses_result = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = trim($_POST['address']);
    $course_id = $_POST['course_id'] ?: 'NULL';

    if (empty($name)) {
        $error = "Name is required";
    } else {
        // Handle photo upload
        $photo = $student['photo'];
        if ($_FILES['photo']['name']) {
            $target_dir = "../../uploads/student_photos/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
            $file_name = basename($_FILES['photo']['name']);
            $target_file = $target_dir . time() . '_' . $file_name;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                $photo = $target_file;
            }
        }

        $email_safe = mysqli_real_escape_string($conn, $email);
        $name_safe = mysqli_real_escape_string($conn, $name);
        $photo_part = $photo ? "'$photo'" : "NULL";
        $course_part = ($course_id !== 'NULL') ? $course_id : 'NULL';
        
        // Update student record
        $phone_safe = mysqli_real_escape_string($conn, $phone);
        $address_safe = mysqli_real_escape_string($conn, $address);
        $gender_safe = mysqli_real_escape_string($conn, $gender);
        $dob_safe = mysqli_real_escape_string($conn, $dob);
        
        $sql = "UPDATE students SET name='$name_safe', email='$email_safe', phone='$phone_safe', gender='$gender_safe', dob='$dob_safe', address='$address_safe', course_id=$course_part, photo=$photo_part WHERE id='$id'";
        
        if (mysqli_query($conn, $sql)) {
            // Also sync user account if email changed
            if ($student['email'] !== $email) {
                $old_email = mysqli_real_escape_string($conn, $student['email']);
                $user_update = "UPDATE users SET name='$name_safe', email='$email_safe' WHERE email='$old_email'";
                mysqli_query($conn, $user_update);
            } else {
                // Just update name in user account
                $old_email = mysqli_real_escape_string($conn, $student['email']);
                $user_update = "UPDATE users SET name='$name_safe' WHERE email='$email_safe'";
                mysqli_query($conn, $user_update);
            }
            header("Location: view.php?msg=Student updated successfully");
            exit();
        } else {
            $error = "Error updating student: " . mysqli_error($conn);
        }
    }
}
?>

<div class="content">
    <h2>Edit Student</h2>

    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>

    <form method="POST" enctype="multipart/form-data" class="form-container">
        <div class="form-row">
            <div class="form-group">
                <label>Enrollment Number</label>
                <input type="text" value="<?php echo htmlspecialchars($student['enrollment_no']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Gender</label>
                <select name="gender">
                    <option value="Male" <?php echo ($student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo ($student['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?php echo htmlspecialchars($student['dob'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Course</label>
                <select name="course_id">
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course) { ?>
                        <option value="<?php echo $course['id']; ?>" <?php echo ($student['course_id'] == $course['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Photo</label>
            <input type="file" name="photo" accept="image/*">
            <?php if ($student['photo']) { ?>
                <p>Current: <img src="<?php echo $student['photo']; ?>" width="50" alt="Student Photo"></p>
            <?php } ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Update Student</button>
            <a href="view.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>