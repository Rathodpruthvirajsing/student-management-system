<?php
include "../auth/session.php";
include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$sql = "SELECT id, name, email, role, created_at FROM users ORDER BY role, name ASC";
$users = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>👥 User Management</h2>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><span class="badge-role <?php echo strtolower($u['role']); ?>"><?php echo ucfirst($u['role']); ?></span></td>
                    <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn-secondary"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.badge-role { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
.badge-role.admin { background: #fee2e2; color: #991b1b; }
.badge-role.teacher { background: #e0f2fe; color: #075985; }
.badge-role.student { background: #dcfce7; color: #166534; }
.badge-role.parent { background: #fef3c7; color: #92400e; }
</style>

<?php include "../includes/footer.php"; ?>
