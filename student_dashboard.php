<?php
include "auth/session.php";
include "config/db.php";

// Ensure role exists and is student
if (!isset($_SESSION['role'])) {
    // Log missing role
    $dbg = date('c') . " DASHBOARD ACCESS: missing role; user_id=" . ($_SESSION['user_id'] ?? 'NONE') . "\n";
    @file_put_contents(__DIR__ . "/logs/redirect_debug.log", $dbg, FILE_APPEND);
    header("Location: home.php?error=Unauthorized+access");
    exit();
}
if ($_SESSION['role'] !== 'student') {
    // Log wrong role access
    $dbg = date('c') . " DASHBOARD ACCESS: wrong role=" . $_SESSION['role'] . "; user_id=" . ($_SESSION['user_id'] ?? 'NONE') . "\n";
    @file_put_contents(__DIR__ . "/logs/redirect_debug.log", $dbg, FILE_APPEND);
    // Not a student — send to appropriate dashboard
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: home.php?error=Unauthorized+access");
    }
    exit();
}

// GET STUDENT DATA FIRST - BEFORE INCLUDING HEADER
$user_id = $_SESSION['user_id'];

// Get user email first
$user_email_query = "SELECT email FROM users WHERE id='$user_id' LIMIT 1";
$user_email_result = mysqli_query($conn, $user_email_query);

if (!$user_email_result || mysqli_num_rows($user_email_result) == 0) {
    session_destroy();
    header("Location: index.php?error=User not found in database");
    exit();
}

$user_email_row = mysqli_fetch_assoc($user_email_result);
$user_email = $user_email_row['email'];

// Now get student record by email
$student_query = "SELECT s.*, c.course_name FROM students s 
                  LEFT JOIN courses c ON s.course_id = c.id 
                  WHERE s.email = '" . mysqli_real_escape_string($conn, $user_email) . "'";
$student_result = mysqli_query($conn, $student_query);

if (!$student_result) {
    die("Database Error: " . mysqli_error($conn));
}

$student = mysqli_fetch_assoc($student_result);

// Check if student record exists - REDIRECT BEFORE ANY OUTPUT
if (!$student) {
    session_destroy();
    header("Location: index.php?error=Student record not found. Contact administrator.");
    exit();
}

$student_id = $student['id'];

// NOW INCLUDE HEADER AND SIDEBAR (SAFE TO OUTPUT)
include "includes/header.php";
include "includes/sidebar.php";

// Get student attendance stats
$attendance_query = "SELECT 
                    COUNT(*) as total_classes,
                    SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present_count
                    FROM attendance WHERE student_id='$student_id'";
$attendance_data = mysqli_fetch_assoc(mysqli_query($conn, $attendance_query));

// Calculate attendance percentage
$attendance_percentage = $attendance_data['total_classes'] > 0 
    ? round(($attendance_data['present_count'] / $attendance_data['total_classes']) * 100, 2)
    : 0;

// Get student marks count
$marks_query = "SELECT COUNT(*) as total_exams FROM marks WHERE student_id='$student_id'";
$marks_data = mysqli_fetch_assoc(mysqli_query($conn, $marks_query));

// Get fee information
$total_fee = 0;
$paid_amount = 0;
$pending_amount = 0;

if ($student['course_id']) {
    $fee_query = "SELECT fs.total_fee, COALESCE(SUM(fp.amount_paid), 0) as paid_amount
                  FROM fee_structure fs
                  LEFT JOIN fee_payments fp ON fp.student_id = '$student_id'
                  WHERE fs.course_id = '" . intval($student['course_id']) . "'
                  GROUP BY fs.total_fee";
    $fee_result = mysqli_query($conn, $fee_query);
    $fee_info = mysqli_fetch_assoc($fee_result);
    
    $total_fee = $fee_info['total_fee'] ?? 0;
    $paid_amount = $fee_info['paid_amount'] ?? 0;
    $pending_amount = $total_fee - $paid_amount;
}
?>

<div class="content">
    <h2 class="dashboard-title">🎒 Student Dashboard — Welcome, <?php echo htmlspecialchars($student['name']); ?></h2>

    <!-- Student Profile Section -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #667eea;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <p><strong>📧 Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
                <p><strong>🆔 Enrollment No:</strong> <?php echo htmlspecialchars($student['enrollment_no']); ?></p>
            </div>
            <div>
                <p><strong>📚 Course:</strong> <?php echo htmlspecialchars($student['course_name'] ?? 'Not Assigned'); ?></p>
                <p><strong>📱 Phone:</strong> <?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="card-container">
        <div class="card card-blue">
            <div class="card-number"><?php echo $attendance_percentage; ?>%</div>
            <div class="card-label">Attendance</div>
            <div class="card-icon">📋</div>
        </div>

        <div class="card card-green">
            <div class="card-number"><?php echo $marks_data['total_exams'] ?? 0; ?></div>
            <div class="card-label">Exams Taken</div>
            <div class="card-icon">📝</div>
        </div>

        <div class="card card-orange">
            <div class="card-number">₹<?php echo number_format($paid_amount, 0); ?></div>
            <div class="card-label">Fees Paid</div>
            <div class="card-icon">💰</div>
        </div>

        <div class="card card-purple">
            <div class="card-number">₹<?php echo number_format($pending_amount, 0); ?></div>
            <div class="card-label">Pending</div>
            <div class="card-icon">⏳</div>
        </div>
    </div>

    <!-- Quick Links -->
    <div style="margin-top: 40px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <a href="student_info.php" style="padding: 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 10px;">
            <span style="font-size: 32px;">📚</span>
            <span>Course & Teacher</span>
        </a>
        
        <a href="modules/attendance/student_view.php" style="padding: 25px; background: linear-gradient(135deg, #2196F3 0%, #1367a0 100%); color: white; text-align: center; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 10px;">
            <span style="font-size: 32px;">📋</span>
            <span>My Attendance</span>
        </a>
        
        <a href="modules/exams/student_results.php" style="padding: 25px; background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); color: white; text-align: center; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 10px;">
            <span style="font-size: 32px;">📝</span>
            <span>My Results</span>
        </a>
        
        <a href="modules/fees/student_fees.php" style="padding: 25px; background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%); color: #333; text-align: center; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 16px; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 10px;">
            <span style="font-size: 32px;">💰</span>
            <span>Fee Status</span>
        </a>
    </div>

    <!-- Download Reports -->
    <div style="margin-top: 40px; padding: 25px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #17a2b8;">
        <h3 style="margin-top: 0; color: #333;">📥 Download Reports</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="reports/student_attendance_report.php" style="padding: 12px 25px; background: #17a2b8; color: white; border-radius: 4px; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                📋 Download Attendance Report
            </a>
            <a href="reports/student_fees_report.php" style="padding: 12px 25px; background: #28a745; color: white; border-radius: 4px; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                💰 Download Fees Report
            </a>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
