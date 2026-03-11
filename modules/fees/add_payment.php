<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$error = '';
$success = '';

// Fetch students
$students_result = mysqli_query($conn, "SELECT s.id, s.name, s.enrollment_no, c.course_name FROM students s LEFT JOIN courses c ON s.course_id = c.id ORDER BY s.name ASC");
$students = mysqli_fetch_all($students_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $amount_paid = $_POST['amount_paid'];
    $payment_date = $_POST['payment_date'];
    $payment_mode = $_POST['payment_mode'];

    if (empty($student_id) || empty($amount_paid) || empty($payment_date)) {
        $error = "All fields are required";
    } else {
        $sql = "INSERT INTO fee_payments (student_id, amount_paid, payment_date, payment_mode) 
                VALUES ('$student_id', '$amount_paid', '$payment_date', '$payment_mode')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: payment.php?msg=Payment recorded successfully");
            exit();
        } else {
            $error = "Error recording payment: " . mysqli_error($conn);
        }
    }
}
?>

<div class="content">
    <h2>Add Fee Payment</h2>

    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>

    <form method="POST" class="form-container">
        <div class="form-group">
            <label>Student *</label>
            <select name="student_id" required>
                <option value="">Select Student</option>
                <?php foreach ($students as $student) { ?>
                    <option value="<?php echo $student['id']; ?>">
                        <?php echo htmlspecialchars($student['name'] . ' (' . $student['enrollment_no'] . ')'); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Amount Paid (Rs.) *</label>
                <input type="number" name="amount_paid" step="0.01" placeholder="1000.00" required value="<?php echo isset($_POST['amount_paid']) ? htmlspecialchars($_POST['amount_paid']) : ''; ?>">
            </div>

            <div class="form-group">
                <label>Payment Date *</label>
                <input type="date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Payment Mode</label>
            <select name="payment_mode">
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="UPI">UPI</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Record Payment</button>
            <a href="payment.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
