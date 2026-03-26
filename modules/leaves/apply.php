<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!in_array($_SESSION['role'], ['student', 'teacher'])) {
    header("Location: ../../home.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $leave_type = mysqli_real_escape_string($conn, trim($_POST['leave_type']));
    $start_date = mysqli_real_escape_string($conn, trim($_POST['start_date']));
    $end_date = mysqli_real_escape_string($conn, trim($_POST['end_date']));
    $reason = mysqli_real_escape_string($conn, trim($_POST['reason']));

    // Simple validation: start_date <= end_date
    if (strtotime($start_date) > strtotime($end_date)) {
        $error = "End Date cannot be before Start Date.";
    } else {
        $sql = "INSERT INTO leaves (user_id, role, leave_type, start_date, end_date, reason) 
                VALUES ($user_id, '$role', '$leave_type', '$start_date', '$end_date', '$reason')";
        if (mysqli_query($conn, $sql)) {
            header("Location: my_leaves.php?msg=" . urlencode("Leave application submitted successfully."));
            exit();
        } else {
            $error = "Failed to submit request: " . mysqli_error($conn);
        }
    }
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <div class="header-section">
        <h2 class="dashboard-title"><i class="fas fa-notes-medical"></i> Apply for Leave</h2>
        <a href="my_leaves.php" class="btn btn-cancel">My History</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="form-container" style="max-width: 700px; margin: 0 auto;">
        <form method="POST" action="">
            <div class="form-group">
                <label>Leave Type</label>
                <select name="leave_type" required>
                    <option value="">-- Select Type --</option>
                    <option value="Sick Leave">Sick Leave</option>
                    <option value="Casual Leave">Casual Leave</option>
                    <option value="Emergency">Emergency</option>
                    <option value="Medical">Medical / Maternity / Paternity</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>From Date</label>
                    <input type="date" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>To Date</label>
                    <input type="date" name="end_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Reason for Leave</label>
                <textarea name="reason" rows="4" required placeholder="Please briefly explain why you are requesting leave..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-add" style="font-size: 16px; padding: 12px 25px;"><i class="fas fa-paper-plane"></i> Submit Application</button>
            </div>
        </form>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
