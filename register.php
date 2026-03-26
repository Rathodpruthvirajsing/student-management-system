<?php
include "config/db.php";
session_start();

$error = '';
$success = '';

// Get registration role from URL or default to 'student'
$registration_role = isset($_GET['role']) && in_array($_GET['role'], ['student', 'admin', 'teacher', 'parent']) ? $_GET['role'] : 'student';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = isset($_POST['role']) && in_array($_POST['role'], ['student', 'admin', 'teacher', 'parent']) ? $_POST['role'] : 'student';
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All required fields must be filled";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Check if email already exists
        $email = mysqli_real_escape_string($conn, $email);
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Email already registered. Please login or use a different email.";
        } else {
            // Additional validation for student role
            if ($role === 'student') {
                $phone = trim($_POST['phone'] ?? '');
                $gender = trim($_POST['gender'] ?? '');
                $dob = trim($_POST['dob'] ?? '');
                $address = trim($_POST['address'] ?? '');
                $course_id = $_POST['course_id'] ?? NULL;
                $enrollment_no = trim($_POST['enrollment_no'] ?? '');
                
                // Check if enrollment number exists
                if (!empty($enrollment_no)) {
                    $enrollment_no = mysqli_real_escape_string($conn, $enrollment_no);
                    $check_enrollment = mysqli_query($conn, "SELECT id FROM students WHERE enrollment_no='$enrollment_no'");
                    
                    if (mysqli_num_rows($check_enrollment) > 0) {
                        $error = "Enrollment number already exists";
                    }
                } else {
                    $error = "Enrollment number is required for students";
                }
            }
            
            if (empty($error)) {
                // Hash password
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $password_hash = mysqli_real_escape_string($conn, $password_hash);
                
                // Insert into users table
                $name = mysqli_real_escape_string($conn, $name);
                $sql_user = "INSERT INTO users (name, email, password, role) 
                             VALUES ('$name', '$email', '$password_hash', '$role')";
                
                if (mysqli_query($conn, $sql_user)) {
                    $user_id = mysqli_insert_id($conn);
                    
                    // If student, also insert into students table
                    if ($role === 'student') {
                        $phone = mysqli_real_escape_string($conn, $phone);
                        $address = mysqli_real_escape_string($conn, $address);
                        $course_id = $course_id ? intval($course_id) : NULL;
                        $gender = mysqli_real_escape_string($conn, $gender);
                        $dob = mysqli_real_escape_string($conn, $dob);
                        $enrollment_no = mysqli_real_escape_string($conn, $enrollment_no);
                        $admission_date = date('Y-m-d');
                        
                        $sql_student = "INSERT INTO students 
                                       (enrollment_no, name, email, phone, gender, dob, address, course_id, admission_date) 
                                       VALUES ('$enrollment_no', '$name', '$email', '$phone', '$gender', '$dob', '$address', $course_id, '$admission_date')";
                        
                        if (mysqli_query($conn, $sql_student)) {
                            $success = "✓ Student registration successful! Please login with your email and password.";
                        } else {
                            mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");
                            $error = "Error creating student record: " . mysqli_error($conn);
                        }
                    } elseif ($role === 'teacher') {
                        // Insert into teachers table
                        $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
                        $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 'NULL';
                        
                        $sql_teacher = "INSERT INTO teachers (name, email, phone, course_id) VALUES ('$name', '$email', '$phone', $course_id)";
                        if (mysqli_query($conn, $sql_teacher)) {
                            $success = "✓ Teacher account created! Please login.";
                        } else {
                            mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");
                            $error = "Teacher record failed: " . mysqli_error($conn);
                        }
                    } elseif ($role === 'parent') {
                        // Insert into parents table
                        $selected_student_id = isset($_POST['student_id']) && !empty($_POST['student_id']) ? intval($_POST['student_id']) : NULL;
                        
                        if ($selected_student_id === NULL) {
                            mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");
                            $error = "Please select a student (your child) from the list.";
                        } else {
                            // Verify student exists
                            $verify_student = mysqli_query($conn, "SELECT id FROM students WHERE id=$selected_student_id LIMIT 1");
                            if (!$verify_student || mysqli_num_rows($verify_student) === 0) {
                                mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");
                                $error = "Selected student not found. Please try again.";
                            } else {
                                $sql_parent = "INSERT INTO parents (name, email, student_id) VALUES ('$name', '$email', $selected_student_id)";
                                if (mysqli_query($conn, $sql_parent)) {
                                    $success = "✓ Parent account created! Please login.";
                                } else {
                                    mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");
                                    $error = "Parent record failed: " . mysqli_error($conn);
                                }
                            }
                        }
                    } else {
                        // Admin registration
                        $success = "✓ Admin account created successfully! Please login.";
                    }
                    
                    if ($success) $_POST = [];
                } else {
                    $error = "Error during registration: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Fetch courses
$courses_result = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

// Fetch students for parent registration (student selection dropdown)
$students_for_parent = [];
if ($registration_role === 'parent') {
    $students_result = mysqli_query($conn, "SELECT s.id, s.name, s.enrollment_no, c.course_name FROM students s LEFT JOIN courses c ON s.course_id = c.id ORDER BY s.name ASC");
    if ($students_result) {
        $students_for_parent = mysqli_fetch_all($students_result, MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo ($registration_role === 'admin' ? 'Admin' : 'Student'); ?> Registration - Student Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .register-body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .register-header h2 {
            margin: 0;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .register-header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .register-form {
            padding: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .required {
            color: #dc3545;
        }
        .btn-register {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 4px solid #28a745;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 4px solid #dc3545;
        }
        .tip {
            background: #e7f3ff;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #2196F3;
            font-size: 12px;
            color: #0056b3;
            margin-bottom: 15px;
        }
        @media (max-width: 600px) {
            .register-container {
                max-width: 100%;
            }
            .register-form {
                padding: 20px;
            }
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="register-body">

<div class="register-container">
    <div class="register-header">
        <h2>
            <?php 
            switch($registration_role) {
                case 'admin': echo '👨‍💼 Admin Registration'; break;
                case 'teacher': echo '👨‍🏫 Teacher Registration'; break;
                case 'parent': echo '👪 Parent Registration'; break;
                default: echo '👨‍🎓 Student Registration';
            }
            ?>
        </h2>
        <p>Create your account to access the system</p>
    </div>

    <div class="register-form">
        <?php if ($success): ?>
            <div class="alert-success"><?php echo $success; ?></div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="login_selection.php" style="display: inline-block; background: #28a745; color: white; padding: 10px 30px; border-radius: 4px; text-decoration: none; font-weight: 600;">✓ Go to Login</a>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="tip">
                📝 <strong>Note:</strong> Fields marked with <span class="required">*</span> are required
            </div>

            <form method="POST">
                <input type="hidden" name="role" value="<?php echo htmlspecialchars($registration_role); ?>">
                
                <?php if ($registration_role === 'student'): ?>
                    <!-- STUDENT REGISTRATION FORM (Existing code) -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name <span class="required">*</span></label>
                            <input type="text" name="name" placeholder="Enter your full name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Enrollment Number <span class="required">*</span></label>
                            <input type="text" name="enrollment_no" placeholder="e.g., ENR001" value="<?php echo htmlspecialchars($_POST['enrollment_no'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" placeholder="your.email@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Password <span class="required">*</span></label>
                            <input type="password" name="password" placeholder="Min. 6 characters" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password <span class="required">*</span></label>
                            <input type="password" name="confirm_password" placeholder="Re-enter password" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" placeholder="+91 XXXXXXXXXX" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Course</label>
                            <select name="course_id">
                                <option value="">Select Course</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>" <?php echo (isset($_POST['course_id']) && $_POST['course_id'] == $course['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($course['course_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" placeholder="Enter your address"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn-register">📝 Create Student Account</button>

                <?php elseif ($registration_role === 'teacher'): ?>
                    <!-- TEACHER REGISTRATION FORM -->
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="name" placeholder="Enter your full name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" placeholder="your.email@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Password <span class="required">*</span></label>
                            <input type="password" name="password" placeholder="Min. 6 characters" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password <span class="required">*</span></label>
                            <input type="password" name="confirm_password" placeholder="Re-enter password" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" placeholder="+91 XXXXXXXXXX" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Assigned Course</label>
                            <select name="course_id">
                                <option value="">Select Course</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>">
                                        <?php echo htmlspecialchars($course['course_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn-register">👨‍🏫 Create Teacher Account</button>

                <?php elseif ($registration_role === 'parent'): ?>
                    <!-- PARENT REGISTRATION FORM -->
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="name" placeholder="Enter your full name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" placeholder="your.email@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Password <span class="required">*</span></label>
                            <input type="password" name="password" placeholder="Min. 6 characters" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password <span class="required">*</span></label>
                            <input type="password" name="confirm_password" placeholder="Re-enter password" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Select Your Child (Student) <span class="required">*</span></label>
                        <select name="student_id" id="student_select" required style="padding: 12px; font-size: 14px;">
                            <option value="">-- Select your child --</option>
                            <?php if (count($students_for_parent) > 0): ?>
                                <?php foreach ($students_for_parent as $student): ?>
                                    <option value="<?php echo $student['id']; ?>" 
                                        <?php echo (isset($_POST['student_id']) && $_POST['student_id'] == $student['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($student['name']); ?> — Enrollment: <?php echo htmlspecialchars($student['enrollment_no']); ?>
                                        <?php if (!empty($student['course_name'])): ?>
                                            (<?php echo htmlspecialchars($student['course_name']); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No students registered yet</option>
                            <?php endif; ?>
                        </select>
                        <small style="color: #666; display: block; margin-top: 5px;">👆 Select the student you are a parent/guardian of from the list above.</small>
                    </div>

                    <button type="submit" class="btn-register">👪 Create Parent Account</button>

                <?php else: ?>
                    <!-- ADMIN REGISTRATION FORM -->
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="name" placeholder="Enter your full name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" placeholder="your.email@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Password <span class="required">*</span></label>
                            <input type="password" name="password" placeholder="Min. 6 characters" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password <span class="required">*</span></label>
                            <input type="password" name="confirm_password" placeholder="Re-enter password" required>
                        </div>
                    </div>

                    <div class="alert-error" style="background: #fff3cd; border-left-color: #ffc107; color: #856404; font-size: 13px;">
                        ⚠️ <strong>Note:</strong> Admin accounts have system-wide access.
                    </div>

                    <button type="submit" class="btn-register">👨‍💼 Create Admin Account</button>

                <?php endif; ?>

                <div class="login-link">
                    Already have an account? <a href="login_selection.php">Login here</a>
                    <br><br>
                    <a href="registration_selection.php" style="font-size: 12px; color: #666;">Change Registration Role</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
