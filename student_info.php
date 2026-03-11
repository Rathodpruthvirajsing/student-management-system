<?php
include "auth/session.php";
include "config/db.php";

// Check if student
if ($_SESSION['role'] !== 'student') {
    header("Location: dashboard.php");
    exit();
}

include "includes/header.php";
include "includes/sidebar.php";

// Get student info
$user_id = $_SESSION['user_id'];
$student_query = "SELECT s.*, c.course_name, c.course_code, c.duration 
                  FROM students s 
                  LEFT JOIN courses c ON s.course_id = c.id 
                  WHERE s.email = (SELECT email FROM users WHERE id='$user_id')";
$student_result = mysqli_query($conn, $student_query);
$student = mysqli_fetch_assoc($student_result);

// Check if student exists
if (!$student) {
    header("Location: auth/logout.php");
    exit();
}

// Get teacher(s) for this course
$teachers = [];
if ($student['course_id']) {
    $teacher_query = "SELECT * FROM teachers WHERE course_id='" . intval($student['course_id']) . "'";
    $teacher_result = mysqli_query($conn, $teacher_query);
    $teachers = mysqli_fetch_all($teacher_result, MYSQLI_ASSOC);
}
?>

<div class="content">
    <h2 class="dashboard-title">📚 My Course & Teacher Details</h2>

    <!-- Student Profile Section -->
    <div style="background: #f8f9fa; padding: 25px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #667eea;">
        <h3 style="margin-bottom: 15px; color: #333;">👤 My Profile</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                <p><strong>Enrollment No:</strong> <?php echo htmlspecialchars($student['enrollment_no']); ?></p>
            </div>
            <div>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($student['gender'] ?? 'N/A'); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo $student['dob'] ? date('d-M-Y', strtotime($student['dob'])) : 'N/A'; ?></p>
            </div>
        </div>
    </div>

    <!-- Course Details Section -->
    <?php if ($student['course_name']): ?>
    <div style="background: white; padding: 25px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #28a745; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 style="margin-bottom: 15px; color: #333;">📚 Course Details</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div>
                <p style="font-size: 12px; color: #999; margin-bottom: 5px;">COURSE NAME</p>
                <p style="font-size: 18px; font-weight: 600; color: #333;"><?php echo htmlspecialchars($student['course_name']); ?></p>
            </div>
            <div>
                <p style="font-size: 12px; color: #999; margin-bottom: 5px;">COURSE CODE</p>
                <p style="font-size: 18px; font-weight: 600; color: #333;"><?php echo htmlspecialchars($student['course_code']); ?></p>
            </div>
            <div>
                <p style="font-size: 12px; color: #999; margin-bottom: 5px;">DURATION</p>
                <p style="font-size: 18px; font-weight: 600; color: #333;"><?php echo htmlspecialchars($student['duration']); ?></p>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #ffc107;">
        <p style="margin: 0; color: #856404;">⚠️ No course assigned yet</p>
    </div>
    <?php endif; ?>

    <!-- Teachers Section -->
    <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 style="margin-bottom: 20px; color: #333;">👨‍🏫 Teacher Details</h3>
        
        <?php if (count($teachers) > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <?php foreach ($teachers as $teacher): ?>
                <div style="padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; background: #f9f9f9;">
                    <div style="font-size: 24px; margin-bottom: 10px;">👨‍🏫</div>
                    <p style="margin: 8px 0;"><strong>Name:</strong> <?php echo htmlspecialchars($teacher['name']); ?></p>
                    <p style="margin: 8px 0;"><strong>Email:</strong> <?php echo htmlspecialchars($teacher['email'] ?? 'N/A'); ?></p>
                    <p style="margin: 8px 0;"><strong>Phone:</strong> <?php echo htmlspecialchars($teacher['phone'] ?? 'N/A'); ?></p>
                    <p style="margin: 8px 0; padding-top: 8px; border-top: 1px solid #ddd; margin-top: 10px;">
                        <strong>Joined:</strong> <?php echo date('d-M-Y', strtotime($teacher['created_at'])); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="padding: 30px; text-align: center; background: #f5f5f5; border-radius: 8px; color: #666;">
                <p>No teachers assigned to your course yet</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Address Section -->
    <?php if ($student['address']): ?>
    <div style="background: white; padding: 25px; border-radius: 8px; margin-top: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 style="margin-bottom: 15px; color: #333;">📍 Address</h3>
        <p style="color: #666; line-height: 1.6;"><?php echo htmlspecialchars($student['address']); ?></p>
    </div>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
