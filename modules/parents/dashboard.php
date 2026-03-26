<?php
include "../../auth/session.php";
include "../../config/db.php";

// Ensure role exists and is parent
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../../index.php?error=Unauthorized+access");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

$user_email = $_SESSION['user_email'];

// Get parent details
$parent_query = "SELECT p.*, s.name as student_name, s.enrollment_no, s.id as child_id FROM parents p 
                 LEFT JOIN students s ON p.student_id = s.id 
                 WHERE p.email = '" . mysqli_real_escape_string($conn, $user_email) . "'";
$parent_result = mysqli_query($conn, $parent_query);
$parent = mysqli_fetch_assoc($parent_result);

if (!$parent) {
    die("Error: Parent record not found in 'parents' table for $user_email.");
}

$child_id = $parent['child_id'];

// Stats for child
$attendance_percentage = 0;
$fees_paid = 0;
$pending_fees = 0;

if ($child_id) {
    // Child attendance
    $attn_res = mysqli_query($conn, "SELECT COUNT(*) as total, SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present FROM attendance WHERE student_id = '$child_id'");
    $attn_row = mysqli_fetch_assoc($attn_res);
    $attendance_percentage = $attn_row['total'] > 0 ? round(($attn_row['present'] / $attn_row['total']) * 100, 2) : 0;

    // Fees
    $fee_res = mysqli_query($conn, "SELECT fs.total_fee, COALESCE(SUM(fp.amount_paid), 0) as paid FROM fee_structure fs 
                                     JOIN students s ON s.course_id = fs.course_id 
                                     LEFT JOIN fee_payments fp ON fp.student_id = s.id 
                                     WHERE s.id = '$child_id' GROUP BY fs.total_fee");
    $fee_row = mysqli_fetch_assoc($fee_res);
    $fees_paid = $fee_row['paid'] ?? 0;
    $pending_fees = ($fee_row['total_fee'] ?? 0) - $fees_paid;
}
?>

<div class="content">
    <h2 class="dashboard-title">👪 Parent Dashboard</h2>
    <p>Welcome back, <strong><?php echo htmlspecialchars($parent['name']); ?></strong>!</p>
    <p>Monitoring Academic Progress for <strong><?php echo htmlspecialchars($parent['student_name'] ?? 'Not Linked'); ?></strong> (Enrollment: <?php echo htmlspecialchars($parent['enrollment_no'] ?? 'N/A'); ?>)</p>

    <div class="card-container">
        <div class="card card-blue">
            <div class="card-number"><?php echo $attendance_percentage; ?>%</div>
            <div class="card-label">Child Attendance</div>
            <div class="card-icon">📋</div>
        </div>

        <div class="card card-green">
            <div class="card-number">Rs. <?php echo number_format($fees_paid, 0); ?></div>
            <div class="card-label">Total Fees Paid</div>
            <div class="card-icon">💰</div>
        </div>

        <div class="card card-red">
            <div class="card-number">Rs. <?php echo number_format($pending_fees, 0); ?></div>
            <div class="card-label">Pending Fees</div>
            <div class="card-icon">⏳</div>
        </div>
    </div>

    <div class="dashboard-section">
        <h3>Academic Insights</h3>
        <div class="quick-links">
            <a href="../../modules/attendance/student_view.php?student_id=<?php echo $child_id; ?>" class="quick-link">Detailed Attendance</a>
            <a href="../../modules/exams/student_results.php?student_id=<?php echo $child_id; ?>" class="quick-link">Exam Results</a>
            <a href="../../modules/fees/student_fees.php" class="quick-link">Fee History</a>
            <a href="../../modules/timetable/student_view.php" class="quick-link">Child Schedule</a>
        </div>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
