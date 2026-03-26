<?php
include "../../auth/session.php";
include "../../config/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../home.php");
    exit();
}

// Fetch all leaves joined with user names
$sql = "SELECT l.*, u.name as applicant_name, u.email 
        FROM leaves l 
        JOIN users u ON l.user_id = u.id 
        ORDER BY FIELD(l.status, 'Pending') DESC, l.created_at DESC";
$result = mysqli_query($conn, $sql);

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <h2>Manage Leave Requests</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <div class="card-container" style="display: block;">
        <table class="table">
            <thead>
                <tr>
                    <th>Applicant</th>
                    <th>Role</th>
                    <th>Leave Details</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($row['applicant_name']); ?></strong><br>
                                <small style="color: #7f8c8d;"><?php echo htmlspecialchars($row['email']); ?></small>
                            </td>
                            <td>
                                <?php if ($row['role'] == 'teacher'): ?>
                                    <span style="background: #3498db; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px;">Teacher</span>
                                <?php else: ?>
                                    <span style="background: #9b59b6; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px;">Student</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['leave_type']); ?></strong><br>
                                <small><?php echo date('d M Y', strtotime($row['start_date'])) . ' - ' . date('d M Y', strtotime($row['end_date'])); ?></small>
                            </td>
                            <td style="max-width:250px; font-size:13px;"><?php echo htmlspecialchars($row['reason']); ?></td>
                            <td>
                                <?php if ($row['status'] == 'Approved'): ?>
                                    <span class="status-present" style="background:#d4edda; color:#155724;"><i class="fas fa-check-circle"></i> Approved</span>
                                <?php elseif ($row['status'] == 'Rejected'): ?>
                                    <span class="status-absent" style="background:#f8d7da; color:#721c24;"><i class="fas fa-times-circle"></i> Rejected</span>
                                <?php else: ?>
                                    <span class="status-pending" style="background:#fff3cd; color:#856404;"><i class="fas fa-clock"></i> Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'Pending'): ?>
                                    <form action="process.php" method="POST" style="display:inline-block;">
                                        <input type="hidden" name="leave_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-edit" style="background-color: #2ecc71;" title="Approve Request" onclick="return confirm('Are you sure you want to approve this leave? It will automatically be marked in attendance.');"><i class="fas fa-check"></i></button>
                                        <button type="submit" name="action" value="reject" class="btn btn-delete" style="background-color: #e74c3c;" title="Reject Request"><i class="fas fa-times"></i></button>
                                    </form>
                                <?php else: ?>
                                    <span style="color:#bdc3c7; font-size: 14px;">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No leave applications found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
