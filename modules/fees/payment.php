<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

// Fetch fee payments
$sql = "SELECT fp.*, s.name as student_name, s.enrollment_no, c.course_name, fs.total_fee
        FROM fee_payments fp
        INNER JOIN students s ON fp.student_id = s.id
        LEFT JOIN courses c ON s.course_id = c.id
        LEFT JOIN fee_structure fs ON c.id = fs.course_id
        ORDER BY fp.payment_date DESC";

$result = mysqli_query($conn, $sql);
$payments = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>Fee Payments</h2>
        <a href="add_payment.php" class="btn btn-add">+ Add Payment</a>
    </div>

    <?php if (isset($_GET['msg'])) { ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php } ?>

    <table class="table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Enrollment No</th>
                <th>Course</th>
                <th>Amount</th>
                <th>Payment Date</th>
                <th>Payment Mode</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($payments) > 0) {
                foreach ($payments as $payment) { 
                    $paid = $payment['amount_paid'];
                    $total = $payment['total_fee'];
                    $remaining = $total - $paid;
                    $status = ($remaining <= 0) ? 'Paid' : 'Pending';
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($payment['enrollment_no']); ?></td>
                        <td><?php echo htmlspecialchars($payment['course_name']); ?></td>
                        <td>Rs. <?php echo number_format($payment['amount_paid'], 2); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($payment['payment_date'])); ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_mode']); ?></td>
                        <td><span class="status-<?php echo strtolower($status); ?>"><?php echo $status; ?></span></td>
                        <td>
                            <a href="delete.php?id=<?php echo $payment['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this payment?');">Delete</a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="8" style="text-align:center;">No payment records found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
