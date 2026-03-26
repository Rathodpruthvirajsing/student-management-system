<?php
include "auth/session.php";
include "config/db.php";

if ($_SESSION['role'] !== 'student') {
    header("Location: student_dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$student_query = "SELECT s.* FROM students s WHERE s.email = (SELECT email FROM users WHERE id='$user_id')";
$student = mysqli_fetch_assoc(mysqli_query($conn, $student_query));

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    
    $sql = "UPDATE students SET phone='$phone', address='$address', gender='$gender', dob='$dob' WHERE id=".$student['id'];
    if (mysqli_query($conn, $sql)) {
        $msg = "Profile updated successfully!";
        // Refresh data
        $student = mysqli_fetch_assoc(mysqli_query($conn, $student_query));
    } else {
        $msg = "Error updating profile: " . mysqli_error($conn);
    }
}

include "includes/header.php";
include "includes/sidebar.php";
?>

<div class="content">
    <div class="header-section">
        <h2>✏️ Update My Profile</h2>
    </div>

    <?php if ($msg) echo "<div class='alert-info'>$msg</div>"; ?>

    <form method="POST" class="form-container" style="max-width: 600px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div class="form-group">
            <label>Phone Number</label>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>" placeholder="Enter your phone number">
        </div>

        <div class="form-group">
            <label>Gender</label>
            <select name="gender">
                <option value="Male" <?php echo ($student['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($student['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo ($student['gender'] === 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="dob" value="<?php echo $student['dob']; ?>">
        </div>

        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3" placeholder="Enter your full address"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
        </div>

        <div style="margin-top: 20px;">
            <p style="font-size: 13px; color: #666; background: #f8f9fa; padding: 10px; border-radius: 4px;">
                <strong>Note:</strong> To change your name or email, please contact the administrator.
            </p>
        </div>

        <button type="submit" class="btn btn-add" style="margin-top: 20px; width: 100%;">💾 Save Changes</button>
    </form>
</div>

<?php include "includes/footer.php"; ?>
