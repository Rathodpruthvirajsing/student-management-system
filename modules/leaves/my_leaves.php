<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!in_array($_SESSION['role'], ['student', 'teacher'])) {
    header("Location: ../../home.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM leaves WHERE user_id = $user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <div class="header-section">
        <h2 class="dashboard-title"><i class="fas fa-history"></i> My Leave Applications</h2>
        <a href="apply.php" class="btn btn-add">Apply New Leave</a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <div class="card-container" style="display: block;">
        <table class="table">
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>Dates (From - To)</th>
                    <th>Reason</th>
                    <th>Applied On</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['leave_type']); ?></strong></td>
                            <td><?php echo date('d M Y', strtotime($row['start_date'])) . ' - ' . date('d M Y', strtotime($row['end_date'])); ?></td>
                            <td style="max-width:300px;"><?php echo htmlspecialchars($row['reason']); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <?php if ($row['status'] == 'Approved'): ?>
                                    <span class="status-present" style="background:#d4edda; color:#155724;"><i class="fas fa-check-circle"></i> Approved</span>
                                <?php elseif ($row['status'] == 'Rejected'): ?>
                                    <span class="status-absent" style="background:#f8d7da; color:#721c24;"><i class="fas fa-times-circle"></i> Rejected</span>
                                <?php else: ?>
                                    <span class="status-pending" style="background:#fff3cd; color:#856404;"><i class="fas fa-clock"></i> Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No leave applications found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
