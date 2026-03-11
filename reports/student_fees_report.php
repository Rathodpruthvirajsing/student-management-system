<?php
include "../auth/session.php";
include "../config/db.php";

// Check if student
if ($_SESSION['role'] !== 'student') {
    header("Location: ../dashboard.php");
    exit();
}

// Get student info
$user_id = $_SESSION['user_id'];
$student_query = "SELECT id, email, name, enrollment_no, course_id FROM students WHERE email = (SELECT email FROM users WHERE id='$user_id')";
$student_result = mysqli_query($conn, $student_query);
$student = mysqli_fetch_assoc($student_result);

// Check if student exists
if (!$student) {
    header("Location: ../auth/logout.php");
    exit();
}

$student_id = $student['id'];

// Get course name and fee structure
$fee_query = "SELECT fs.total_fee, c.course_name FROM fee_structure fs 
              JOIN courses c ON fs.course_id = c.id
              WHERE c.id = '" . intval($student['course_id']) . "'";
$fee_result = mysqli_query($conn, $fee_query);
$fee_info = mysqli_fetch_assoc($fee_result);
$course_name = $fee_info['course_name'] ?? 'N/A';
$total_fee = $fee_info['total_fee'] ?? 0;

// Get payment records
$payment_query = "SELECT amount_paid, payment_date, payment_mode 
                  FROM fee_payments
                  WHERE student_id='$student_id'
                  ORDER BY payment_date ASC";
$payment_result = mysqli_query($conn, $payment_query);
$payments = mysqli_fetch_all($payment_result, MYSQLI_ASSOC);

// Calculate totals
$total_paid = 0;
foreach ($payments as $payment) {
    $total_paid += $payment['amount_paid'];
}
$pending_amount = $total_fee - $total_paid;

// Generate HTML
$html = '
<html>
<head>
    <title>Fees Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .info-box { background: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .info-box p { margin: 5px 0; }
        .summary { display: flex; justify-content: space-around; margin: 20px 0; padding: 15px; background: #e8f4f8; border-radius: 5px; }
        .summary-item { text-align: center; }
        .summary-item h3 { margin: 0; color: #667eea; }
        .summary-item p { margin: 5px 0; color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #667eea; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
        .paid { color: #28a745; }
        .pending { color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Fees Report</h1>
        <p>Student Management System</p>
    </div>
    
    <div class="info-box">
        <p><strong>Student Name:</strong> ' . htmlspecialchars($student['name']) . '</p>
        <p><strong>Enrollment No:</strong> ' . htmlspecialchars($student['enrollment_no']) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($student['email']) . '</p>
        <p><strong>Course:</strong> ' . htmlspecialchars($course_name) . '</p>
        <p><strong>Report Generated:</strong> ' . date('d-M-Y H:i:s') . '</p>
    </div>
    
    <div class="summary">
        <div class="summary-item">
            <h3>₹' . number_format($total_fee, 2) . '</h3>
            <p>Total Fee</p>
        </div>
        <div class="summary-item">
            <h3 class="paid">₹' . number_format($total_paid, 2) . '</h3>
            <p>Amount Paid</p>
        </div>
        <div class="summary-item">
            <h3 class="pending">₹' . number_format($pending_amount, 2) . '</h3>
            <p>Pending Amount</p>
        </div>
        <div class="summary-item">
            <h3>' . ($total_fee > 0 ? round(($total_paid / $total_fee) * 100, 1) : 0) . '%</h3>
            <p>Payment %</p>
        </div>
    </div>
    
    <h3 style="margin-top: 30px; color: #333;">Payment History</h3>
    <table>
        <thead>
            <tr>
                <th>Payment Date</th>
                <th>Amount Paid</th>
                <th>Payment Mode</th>
            </tr>
        </thead>
        <tbody>';

if (count($payments) > 0) {
    foreach ($payments as $payment) {
        $html .= '<tr>
                    <td>' . date('d-M-Y', strtotime($payment['payment_date'])) . '</td>
                    <td>₹' . number_format($payment['amount_paid'], 2) . '</td>
                    <td>' . htmlspecialchars($payment['payment_mode']) . '</td>
                </tr>';
    }
} else {
    $html .= '<tr><td colspan="3" style="text-align: center;">No payments recorded yet</td></tr>';
}

$html .= '
        </tbody>
    </table>
    
    <div class="footer">
        <p>This is an auto-generated report from Student Management System</p>
        <p>&copy; 2026 Student Management System. All rights reserved.</p>
    </div>
</body>
</html>';

// Generate filename
$filename = 'Fees_Report_' . $student['enrollment_no'] . '_' . date('d-m-Y') . '.html';

// Output as downloadable file
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $html;
?>
