<?php
session_start();

// Check if authorized (Student or Parent)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['student', 'parent'])) {
    header("Location: ../../index.php?error=Unauthorized+access");
    exit();
}

include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$student_id = null;

if ($_SESSION['role'] === 'student') {
    // Get student info from session
    $user_id = $_SESSION['user_id'];
    $student_query = "SELECT s.*, c.course_name, c.id as course_id FROM students s 
                      LEFT JOIN courses c ON s.course_id = c.id 
                      WHERE s.email = (SELECT email FROM users WHERE id='$user_id')";
    $student_result = mysqli_query($conn, $student_query);
    $student = mysqli_fetch_assoc($student_result);
    if (!$student) { header("Location: ../../auth/logout.php"); exit(); }
    $student_id = $student['id'];
} elseif ($_SESSION['role'] === 'parent') {
    // Get child ID linked to parent
    $user_email = $_SESSION['user_email'];
    $parent_query = "SELECT student_id FROM parents WHERE email = '$user_email'";
    $parent_result = mysqli_query($conn, $parent_query);
    $parent = mysqli_fetch_assoc($parent_result);
    if (!$parent || !$parent['student_id']) { 
        echo "<div class='content'><h2>Error</h2><p>No linked student found for this parent account.</p></div>";
        include "../../includes/footer.php";
        exit(); 
    }
    $student_inner_id = $parent['student_id'];
    $student_query = "SELECT s.*, c.course_name, c.id as course_id FROM students s 
                      LEFT JOIN courses c ON s.course_id = c.id 
                      WHERE s.id = '$student_inner_id'";
    $student_result = mysqli_query($conn, $student_query);
    $student = mysqli_fetch_assoc($student_result);
    $student_id = $student['id'];
}
$course_id = $student['course_id'];

// Get fee structure for student's course
$total_fee = 0;
$fee_structure = [];
if ($course_id) {
    $fee_structure_query = "SELECT * FROM fee_structure WHERE course_id='" . intval($course_id) . "'";
    $fee_structure_result = mysqli_query($conn, $fee_structure_query);
    $fee_structure = mysqli_fetch_assoc($fee_structure_result);
}

$total_fee = $fee_structure['total_fee'] ?? 0;

// Get all payments for this student
$payments = [];
$paid_amount = 0;
if ($student_id) {
    $payments_query = "SELECT * FROM fee_payments WHERE student_id='$student_id' ORDER BY payment_date DESC";
    $payments_result = mysqli_query($conn, $payments_query);
    if ($payments_result) {
        while ($payment = mysqli_fetch_assoc($payments_result)) {
            $payments[] = $payment;
            $paid_amount += $payment['amount_paid'];
        }
    }
}

$pending_amount = $total_fee - $paid_amount;
$payment_percentage = $total_fee > 0 ? round(($paid_amount / $total_fee) * 100, 1) : 0;

// Determine fee status
$status = $pending_amount <= 0 ? 'FULLY PAID' : ($pending_amount > 0 && $pending_amount < $total_fee ? 'PARTIAL' : 'DUE');
$status_color = $status === 'FULLY PAID' ? '#28a745' : ($status === 'PARTIAL' ? '#ffc107' : '#dc3545');
?>

<div class="content">
    <h2 class="dashboard-title">💰 My Fee Status</h2>

    <!-- Student Info -->
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #667eea;">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div>
                <p><strong>👤 Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
                <p><strong>🆔 Enrollment No:</strong> <?php echo htmlspecialchars($student['enrollment_no']); ?></p>
            </div>
            <div>
                <p><strong>📚 Course:</strong> <?php echo htmlspecialchars($student['course_name'] ?? 'Not Assigned'); ?></p>
                <p><strong>📧 Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
            </div>
            <div>
                <p><strong>📱 Phone:</strong> <?php echo htmlspecialchars($student['phone'] ?? 'N/A'); ?></p>
            </div>
        </div>
    </div>

    <!-- Fee Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Total Fee Card -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 8px; color: white;">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: 5px;">TOTAL FEE</div>
            <div style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">₹<?php echo number_format($total_fee, 0); ?></div>
            <div style="font-size: 12px; opacity: 0.8;">Course Fee Amount</div>
        </div>

        <!-- Paid Amount Card -->
        <div style="background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); padding: 25px; border-radius: 8px; color: white;">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: 5px;">AMOUNT PAID</div>
            <div style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">₹<?php echo number_format($paid_amount, 0); ?></div>
            <div style="font-size: 12px; opacity: 0.8;"><?php echo count($payments); ?> payment<?php echo count($payments) !== 1 ? 's' : ''; ?></div>
        </div>

        <!-- Pending Amount Card -->
        <div style="background: linear-gradient(135deg, <?php echo $status === 'FULLY PAID' ? '#17a2b8' : '#dc3545'; ?> 0%, <?php echo $status === 'FULLY PAID' ? '#0e6388' : '#a02622'; ?> 100%); padding: 25px; border-radius: 8px; color: white;">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: 5px;">PENDING AMOUNT</div>
            <div style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">₹<?php echo number_format($pending_amount, 0); ?></div>
            <div style="font-size: 12px; opacity: 0.8; background: rgba(255,255,255,0.2); padding: 5px 10px; border-radius: 4px; display: inline-block; margin-top: 5px;"><?php echo $status; ?></div>
        </div>
    </div>

    <!-- Fee Progress Bar -->
    <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <h3 style="margin-top: 0; color: #333; display: flex; justify-content: space-between; align-items: center;">
            <span>Payment Progress</span>
            <span style="font-size: 18px; color: #667eea;"><?php echo $payment_percentage; ?>%</span>
        </h3>
        <div style="background: #e9ecef; height: 20px; border-radius: 10px; overflow: hidden;">
            <div style="background: linear-gradient(90deg, #28a745 0%, #20c997 100%); height: 100%; width: <?php echo $payment_percentage; ?>%; transition: width 0.3s ease;"></div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
            <div>
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Amount Paid</div>
                <div style="font-size: 20px; font-weight: 600; color: #28a745;">₹<?php echo number_format($paid_amount, 0); ?></div>
            </div>
            <div>
                <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Amount Remaining</div>
                <div style="font-size: 20px; font-weight: 600; color: #dc3545;">₹<?php echo number_format($pending_amount, 0); ?></div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h3 style="margin-top: 0; color: #333;">📋 Payment History</h3>
        
        <?php if (count($payments) > 0): ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #667eea;">
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #333;">Payment Date</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #333;">Amount Paid</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #333;">Payment Method</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #333;">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment):
                            $pay_date = date('d-M-Y', strtotime($payment['payment_date']));
                        ?>
                            <tr style="border-bottom: 1px solid #e9ecef;">
                                <td style="padding: 15px; color: #333;">
                                    📅 <?php echo htmlspecialchars($pay_date); ?>
                                </td>
                                <td style="padding: 15px; color: #28a745; font-weight: 600;">
                                    ₹<?php echo number_format($payment['amount_paid'], 0); ?>
                                </td>
                                <td style="padding: 15px; color: #666;">
                                    <?php 
                                        $method = htmlspecialchars($payment['payment_mode'] ?? 'N/A');
                                        $method_badge = $method === 'Cash' ? '💵' : ($method === 'UPI' ? '📱' : ($method === 'Card' ? '💳' : '🏦'));
                                        echo $method_badge . ' ' . $method;
                                    ?>
                                </td>
                                <td style="padding: 15px; color: #666;">
                                    <?php echo (isset($payment['notes']) && $payment['notes']) ? htmlspecialchars($payment['notes']) : '-'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Payment Summary -->
            <div style="margin-top: 20px; padding: 15px; background: #f0f8ff; border-left: 4px solid #2196F3; border-radius: 4px;">
                <p style="margin: 0; color: #333;">
                    <strong>Total Payments Recorded:</strong> <span style="color: #667eea; font-size: 16px; font-weight: 600;">₹<?php echo number_format($paid_amount, 0); ?></span>
                    <br><small style="color: #666; margin-top: 5px; display: block;">
                        <?php echo count($payments); ?> payment<?php echo count($payments) !== 1 ? 's' : ''; ?> received on your account
                    </small>
                </p>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px;">
                <div style="font-size: 32px; margin-bottom: 10px;">💰</div>
                <p style="color: #666; margin: 10px 0;">No payment records found</p>
                <p style="color: #999; font-size: 14px;">Your fee payments will appear here once approved by the administration.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Fee Breakdown Info -->
    <div style="margin-top: 30px; padding: 20px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 8px;">
        <p style="margin: 0; color: #856404;">
            <strong>ℹ️ Fee Information:</strong> 
            <?php if ($status === 'FULLY PAID'): ?>
                Your fees have been <strong>fully paid</strong>. Thank you for settling your dues on time!
            <?php elseif ($status === 'PARTIAL'): ?>
                Your fees are <strong>partially paid</strong>. Please clear the pending amount of <strong>₹<?php echo number_format($pending_amount, 0); ?></strong> at your earliest convenience.
            <?php else: ?>
                Your fees are <strong>due for payment</strong>. Please contact the administration office for payment details.
            <?php endif; ?>
        </p>
    </div>

    <!-- Back Button -->
    <div style="margin-top: 30px;">
        <a href="../student_dashboard.php" style="display: inline-block; padding: 12px 25px; background: #667eea; color: white; text-decoration: none; border-radius: 4px; font-weight: 600; transition: all 0.3s;">
            ← Back to Dashboard
        </a>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
