<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$role = $_SESSION['role'];

// Admin/Teacher see all notices; Student/Parent see active notices only
if ($role === 'admin' || $role === 'teacher') {
    $sql = "SELECT n.*, u.name as author FROM notices n LEFT JOIN users u ON n.posted_by = u.id ORDER BY n.created_at DESC";
} else {
    $sql = "SELECT n.*, u.name as author FROM notices n LEFT JOIN users u ON n.posted_by = u.id WHERE n.is_active = 1 AND (n.expire_date IS NULL OR n.expire_date >= CURDATE()) ORDER BY n.created_at DESC";
}

$result = mysqli_query($conn, $sql);
$notices = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <?php if ($role === 'admin' || $role === 'teacher'): ?>
            <h2>📢 Notice Board Management</h2>
            <?php if ($role === 'admin'): ?>
                <a href="add.php" class="btn btn-add">+ Post New Notice</a>
            <?php endif; ?>
        <?php else: ?>
            <h2>🔔 School Notifications</h2>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <?php if ($role === 'admin' || $role === 'teacher'): ?>
        <!-- Management Table for Admin/Teacher -->
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Author</th>
                    <th>Posted Date</th>
                    <th>Expires</th>
                    <?php if ($role === 'admin'): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($notices) > 0): ?>
                    <?php foreach ($notices as $notice): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($notice['title']); ?></strong></td>
                            <td>
                                <?php if ($notice['is_active']): ?>
                                    <span class="status-present">Active</span>
                                <?php else: ?>
                                    <span class="status-absent">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($notice['author'] ?? 'N/A'); ?></td>
                            <td><?php echo date('d-M-Y H:i', strtotime($notice['created_at'])); ?></td>
                            <td><?php echo $notice['expire_date'] ? date('d-M-Y', strtotime($notice['expire_date'])) : 'Never'; ?></td>
                            <?php if ($role === 'admin'): ?>
                            <td>
                                <a href="edit.php?id=<?php echo $notice['id']; ?>" class="btn btn-edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="delete.php?id=<?php echo $notice['id']; ?>" class="btn btn-delete" title="Delete" onclick="return confirm('Delete this notice?');"><i class="fa-solid fa-trash"></i></a>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No notices posted yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

    <?php else: ?>
        <!-- Read-only Notification Cards for Students and Parents -->
        <?php if (count($notices) > 0): ?>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($notices as $notice): ?>
                    <div style="background: white; padding: 20px 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); border-left: 5px solid #667eea; display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                <span style="font-size: 20px;">🔔</span>
                                <h3 style="margin: 0; color: #333; font-size: 17px;"><?php echo htmlspecialchars($notice['title']); ?></h3>
                            </div>
                            <?php if (!empty($notice['content'])): ?>
                                <p style="margin: 0 0 10px 0; color: #555; line-height: 1.6; font-size: 14px;"><?php echo nl2br(htmlspecialchars($notice['content'])); ?></p>
                            <?php endif; ?>
                            <div style="font-size: 12px; color: #999; display: flex; gap: 20px; flex-wrap: wrap;">
                                <span>📅 Posted: <?php echo date('d M Y, h:i A', strtotime($notice['created_at'])); ?></span>
                                <?php if ($notice['author']): ?>
                                    <span>👤 By: <?php echo htmlspecialchars($notice['author']); ?></span>
                                <?php endif; ?>
                                <?php if ($notice['expire_date']): ?>
                                    <span>⏰ Expires: <?php echo date('d M Y', strtotime($notice['expire_date'])); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="white-space: nowrap;">
                            <span style="background: #d4edda; color: #155724; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Active</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; background: white; border-radius: 8px; color: #aaa;">
                <div style="font-size: 48px; margin-bottom: 15px;">🔕</div>
                <p style="font-size: 16px;">No notifications at this time.</p>
                <p style="font-size: 13px;">Check back later for school announcements.</p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include "../../includes/footer.php"; ?>

