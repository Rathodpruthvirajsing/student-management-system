<?php
include "../auth/session.php";
include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

// Generate fee report
$sql = "SELECT s.enrollment_no, s.name, c.course_name, fs.total_fee,
        COALESCE(SUM(fp.amount_paid), 0) as paid,
        (fs.total_fee - COALESCE(SUM(fp.amount_paid), 0)) as remaining
        FROM students s
        LEFT JOIN courses c ON s.course_id = c.id
        LEFT JOIN fee_structure fs ON c.id = fs.course_id
        LEFT JOIN fee_payments fp ON s.id = fp.student_id
        GROUP BY s.id
        ORDER BY s.name ASC";

$result = mysqli_query($conn, $sql);
$records = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <h2>Fee Report</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Course</th>
                <th>Total Fee</th>
                <th>Paid</th>
                <th>Remaining</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_fee = 0;
            $total_paid = 0;
            foreach ($records as $record) { 
                $status = ($record['remaining'] <= 0) ? 'Paid' : 'Pending';
                $status_class = ($record['remaining'] <= 0) ? 'paid' : 'pending';
                $total_fee += $record['total_fee'];
                $total_paid += $record['paid'];
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['name']); ?></td>
                    <td><?php echo htmlspecialchars($record['course_name'] ?? 'N/A'); ?></td>
                    <td>Rs. <?php echo number_format($record['total_fee'], 2); ?></td>
                    <td>Rs. <?php echo number_format($record['paid'], 2); ?></td>
                    <td>Rs. <?php echo number_format($record['remaining'], 2); ?></td>
                    <td><span class="status-<?php echo $status_class; ?>"><?php echo $status; ?></span></td>
                </tr>
            <?php } ?>
            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td colspan="2">TOTAL</td>
                <td>Rs. <?php echo number_format($total_fee, 2); ?></td>
                <td>Rs. <?php echo number_format($total_paid, 2); ?></td>
                <td>Rs. <?php echo number_format($total_fee - $total_paid, 2); ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <button onclick="window.print();" class="btn btn-add" style="margin-top: 20px;">Print Report</button>
</div>

<?php include "../includes/footer.php"; ?>
