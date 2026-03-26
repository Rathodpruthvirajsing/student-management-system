<?php
include "auth/session.php";
include "config/db.php";

// Ensure role exists and is admin
if (!isset($_SESSION['role'])) {
    header("Location: home.php?error=Unauthorized+access");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    // Not an admin — send to appropriate dashboard
    if ($_SESSION['role'] === 'student') {
        header("Location: student_dashboard.php");
    } else {
        header("Location: home.php?error=Unauthorized+access");
    }
    exit();
}

include "includes/header.php";
include "includes/sidebar.php";

// Fetch Counts
$students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM students"));
$courses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM courses"));
$teachers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM teachers"));
$exams = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM exams"));
$attendance = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT student_id) as total FROM attendance WHERE status='Present'"));
$fees_collected = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount_paid), 0) as total FROM fee_payments"));

?>

<div class="content">
    <h2 class="dashboard-title">📊 Admin Dashboard</h2>

    <div class="card-container">
        <div class="card card-blue">
            <div class="card-number"><?php echo $students['total']; ?></div>
            <div class="card-label">Total Students</div>
            <div class="card-icon">👥</div>
        </div>

        <div class="card card-green">
            <div class="card-number"><?php echo $courses['total']; ?></div>
            <div class="card-label">Total Courses</div>
            <div class="card-icon">📚</div>
        </div>

        <div class="card card-purple">
            <div class="card-number"><?php echo $teachers['total']; ?></div>
            <div class="card-label">Total Teachers</div>
            <div class="card-icon">👨‍🏫</div>
        </div>

        <div class="card card-orange">
            <div class="card-number"><?php echo $exams['total']; ?></div>
            <div class="card-label">Total Exams</div>
            <div class="card-icon">📝</div>
        </div>

        <div class="card card-red">
            <div class="card-number"><?php echo $attendance['total']; ?></div>
            <div class="card-label">Present Today</div>
            <div class="card-icon">✓</div>
        </div>

        <div class="card card-teal">
            <div class="card-number">Rs. <?php echo number_format($fees_collected['total'], 0); ?></div>
            <div class="card-label">Total Fees Collected</div>
            <div class="card-icon">💰</div>
        </div>
    </div>


    <div class="dashboard-section">
        <h3>Quick Links</h3>
        <div class="quick-links">
            <a href="modules/students/view.php" class="quick-link">Manage Students</a>
            <a href="modules/courses/view.php" class="quick-link">Manage Courses</a>
            <a href="modules/students/teachers/view.php" class="quick-link">Manage Teachers</a>
            <a href="modules/attendance/mark.php" class="quick-link">Mark Attendance</a>
            <a href="modules/exams/create.php" class="quick-link">Create Exams</a>
            <a href="modules/fees/payment.php" class="quick-link">Record Fees</a>
        </div>
    </div>
</div>


<?php include "includes/footer.php"; ?>
