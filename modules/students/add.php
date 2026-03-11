<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$error = '';
$success = '';

// Fetch all courses for dropdown
$courses_result = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enrollment_no = trim($_POST['enrollment_no']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = trim($_POST['address']);
    $course_id = $_POST['course_id'] ?: 'NULL';
    $admission_date = $_POST['admission_date'];
    $password = trim($_POST['password'] ?? '');

    // Validation
    if (empty($enrollment_no) || empty($name)) {
        $error = "Enrollment number and name are required";
    } elseif (empty($email)) {
        $error = "Email is required for student login";
    } else {
        // Check if enrollment no already exists
        $check = mysqli_query($conn, "SELECT id FROM students WHERE enrollment_no='$enrollment_no'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Enrollment number already exists";
        } else {
            $email_safe = mysqli_real_escape_string($conn, $email);
            $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email='$email_safe'");
            if (mysqli_num_rows($check_email) > 0) {
                $error = "Email already registered";
            } else {
                // Handle photo upload
                $photo = '';
                if ($_FILES['photo']['name']) {
                    $target_dir = "../../uploads/student_photos/";
                    if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
                    $file_name = basename($_FILES['photo']['name']);
                    $target_file = $target_dir . time() . '_' . $file_name;
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                        $photo = $target_file;
                    }
                }

                // Generate password if not provided
                if (empty($password)) {
                    $password = substr(md5(time() . $enrollment_no), 0, 8);
                    $password_display = $password;
                } else {
                    $password_display = "***";
                }

                // Create user account
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $password_hash_safe = mysqli_real_escape_string($conn, $password_hash);
                $name_safe = mysqli_real_escape_string($conn, $name);
                $sql_user = "INSERT INTO users (name, email, password, role) VALUES ('$name_safe', '$email_safe', '$password_hash_safe', 'student')";
                
                if (mysqli_query($conn, $sql_user)) {
                    // Insert student
                    $photo_part = $photo ? "'$photo'" : "NULL";
                    $course_part = ($course_id !== 'NULL') ? $course_id : 'NULL';
                    $phone_safe = mysqli_real_escape_string($conn, $phone);
                    $address_safe = mysqli_real_escape_string($conn, $address);
                    $gender_safe = mysqli_real_escape_string($conn, $gender);
                    $dob_safe = mysqli_real_escape_string($conn, $dob);
                    $enrollment_safe = mysqli_real_escape_string($conn, $enrollment_no);
                    
                    $sql = "INSERT INTO students (enrollment_no, name, email, phone, gender, dob, address, photo, course_id, admission_date) 
                            VALUES ('$enrollment_safe', '$name_safe', '$email_safe', '$phone_safe', '$gender_safe', '$dob_safe', '$address_safe', $photo_part, $course_part, '$admission_date')";
                    
                    if (mysqli_query($conn, $sql)) {
                        $_POST = [];
                        $success = "✓ Student added! Email: $email_safe | Password: $password_display";
                    } else {
                        $user_id = mysqli_insert_id($conn);
                        if ($user_id) mysqli_query($conn, "DELETE FROM users WHERE id='$user_id'");
                        $error = "Error adding student: " . mysqli_error($conn);
                    }
                } else {
                    $error = "Error creating user account: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<div class="content">
    <h2>Add New Student</h2>

    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>
    <?php if ($success) {
        echo '<div class="alert-success" style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; color: #155724; margin-bottom: 20px;">';
        echo '<strong>✓ ' . $success . '</strong><br><br>';
        echo '<a href="view.php" class="btn btn-add" style="display: inline-block;">View All Students →</a>';
        echo '</div>';
    } ?>

    <form method="POST" enctype="multipart/form-data" class="form-container">
        <div class="form-row">
            <div class="form-group">
                <label>Enrollment Number *</label>
                <input type="text" name="enrollment_no" placeholder="e.g., STU001" required value="<?php echo isset($_POST['enrollment_no']) ? htmlspecialchars($_POST['enrollment_no']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" placeholder="Full Name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="Email Address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" placeholder="Phone Number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Gender</label>
                <select name="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Course</label>
                <select name="course_id">
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course) { ?>
                        <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Admission Date</label>
                <input type="date" name="admission_date" value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3" placeholder="Address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
        </div>

        <div class="form-group">
            <label>Password (for login) *</label>
            <input type="text" name="password" placeholder="Leave blank to auto-generate">
            <small style="color: #666; font-size: 12px;">If empty, a random password will be auto-generated</small>
        </div>

        <div class="form-group">
            <label>Photo</label>
            <input type="file" name="photo" accept="image/*">
        </div>

        <div class="alert-info" style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 12px; border-radius: 4px; margin-bottom: 20px; color: #0c5460;">
            <strong>ℹ️ Auto-Login:</strong> A student login account will be created automatically with the email and password above.
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Add Student</button>
            <a href="view.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>