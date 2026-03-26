<?php
include "../../auth/session.php";
include "../../config/db.php";

if ($_SESSION['role'] !== 'parent') {
    header("Location: ../../index.php");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

$user_email = $_SESSION['user_email'];

// Get parent and linked child info
$parent_query = "SELECT p.*, s.name as student_name, s.enrollment_no, s.email as student_email, s.phone as student_phone, s.gender as student_gender, s.dob as student_dob, s.address as student_address, c.course_name 
                 FROM parents p 
                 LEFT JOIN students s ON p.student_id = s.id 
                 LEFT JOIN courses c ON s.course_id = c.id
                 WHERE p.email = '" . mysqli_real_escape_string($conn, $user_email) . "'";
$parent_result = mysqli_query($conn, $parent_query);
$parent = mysqli_fetch_assoc($parent_result);

if (!$parent) {
    die("Error: Parent record not found.");
}
?>

<div class="content">
    <div class="header-section">
        <h2>👤 Child Information Profile</h2>
    </div>

    <div class="card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
            <div style="width: 60px; height: 60px; background: #e0f2fe; color: #075985; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px;">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <h3 style="margin: 0; color: #333;"><?php echo htmlspecialchars($parent['student_name'] ?? 'Not Linked'); ?></h3>
                <p style="margin: 5px 0 0 0; color: #666;">Enrollment No: <?php echo htmlspecialchars($parent['enrollment_no'] ?? 'N/A'); ?></p>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div>
                <h4 style="color: #764ba2; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px; margin-bottom: 15px;"><i class="fas fa-info-circle"></i> Basic Details</h4>
                <p><strong>Course:</strong> <?php echo htmlspecialchars($parent['course_name'] ?? 'N/A'); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($parent['student_gender'] ?? 'N/A'); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo $parent['student_dob'] ? date('d M Y', strtotime($parent['student_dob'])) : 'N/A'; ?></p>
            </div>

            <div>
                <h4 style="color: #764ba2; border-bottom: 2px solid #f0f0f0; padding-bottom: 8px; margin-bottom: 15px;"><i class="fas fa-address-book"></i> Contact Details</h4>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($parent['student_email'] ?? 'N/A'); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($parent['student_phone'] ?? 'N/A'); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($parent['student_address'] ?? 'N/A'); ?></p>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px; background: #fff8e1; border-left: 4px solid #ffc107; padding: 15px; border-radius: 4px;">
        <p style="margin: 0; color: #856404; font-size: 14px;">
            <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> If any information above is incorrect, please contact the school administration for updates.
        </p>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
