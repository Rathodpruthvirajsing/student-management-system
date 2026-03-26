<?php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="<?php 
        // Calculate the correct path to assets based on script depth dynamically using filesystem
        $project_root = str_replace('\\', '/', dirname(__DIR__));
        $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
        $relative_path = trim(str_ireplace($project_root, '', $script_dir), '/');
        $depth = $relative_path ? substr_count($relative_path, '/') + 1 : 0;
        $path_prefix = str_repeat('../', $depth);
        echo $path_prefix;
    ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .notification-dropdown { position: absolute; top: 60px; right: 20px; width: 300px; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); z-index: 1000; display: none; overflow: hidden; }
        .notification-item { padding: 12px 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
        .notification-item:hover { background: #f8f9fa; }
        .notification-title { font-weight: 600; font-size: 13px; color: #333; margin-bottom: 3px; }
        .notification-content { font-size: 12px; color: #666; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .notification-badge { background: #e74c3c; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; position: absolute; top: 0; right: -5px; }
        .dismiss-icon { color: #2ecc71; cursor: pointer; font-size: 16px; margin-top: 5px; }
        .dismiss-icon:hover { color: #27ae60; transform: scale(1.2); }
        
        /* Sidebar Toggle Styles */
        #sidebar-toggle { cursor: pointer; margin-right: 15px; font-size: 22px; color: #ecf0f1; transition: 0.3s; padding: 5px; border-radius: 4px; }
        #sidebar-toggle:hover { background: rgba(255,255,255,0.1); }
        .sidebar.hidden { width: 0; overflow: hidden; display: none; }
        .sidebar-hidden-active .content { margin-left: 0; width: 100%; transition: all 0.3s ease; }
    </style>
    <script>
        function toggleSidebar() {
            var sidebar = document.querySelector('.sidebar');
            var container = document.querySelector('.container');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                container.classList.remove('sidebar-hidden-active');
            } else {
                sidebar.classList.add('hidden');
                container.classList.add('sidebar-hidden-active');
            }
        }
    </script>
</head>
<body>

<?php
// Notification Logic
$unread_notices = [];
if (isset($_SESSION['user_id']) && isset($conn)) {
    $uid = intval($_SESSION['user_id']);
    
    $notice_sql = "SELECT n.* FROM notices n 
                   LEFT JOIN notice_reads nr ON n.id = nr.notice_id AND nr.user_id = $uid 
                   WHERE n.is_active = 1 
                   AND nr.id IS NULL 
                   AND (n.expire_date IS NULL OR n.expire_date >= CURDATE())
                   ORDER BY n.created_at DESC LIMIT 5";
    $notice_res = mysqli_query($conn, $notice_sql);
    if ($notice_res) {
        $unread_notices = mysqli_fetch_all($notice_res, MYSQLI_ASSOC);
    }
}
$unread_count = count($unread_notices);
?>

<div class="header">
    <div class="header-content">
        <div style="display: flex; align-items: center;">
            <i class="fa-solid fa-bars" id="sidebar-toggle" onclick="toggleSidebar()"></i>
            <?php 
                $portal_title = "SMS Portal";
                if (isset($_SESSION['role'])) {
                    $portal_title = ucfirst($_SESSION['role']) . " Portal";
                }
            ?>
            <h1>🎓 <?php echo $portal_title; ?></h1>
        </div>
        <div class="user-info" style="display: flex; align-items: center; gap: 20px;">
            <?php if (isset($_SESSION['user_id'])): ?>
            <div style="position: relative; cursor: pointer;" onclick="document.getElementById('notifDropdown').style.display = document.getElementById('notifDropdown').style.display === 'block' ? 'none' : 'block'">
                <i class="fa-solid fa-bell" style="font-size: 20px; color: #ecf0f1;"></i>
                <?php if ($unread_count > 0): ?>
                    <span class="notification-badge"><?php echo $unread_count; ?></span>
                <?php endif; ?>
                
                <div id="notifDropdown" class="notification-dropdown">
                    <div style="padding: 10px 15px; background: #2c3e50; color: white; font-weight: 600; font-size: 14px; display: flex; justify-content: space-between;">
                        <span>🔔 Notifications</span>
                        <small><?php echo $unread_count; ?> New</small>
                    </div>
                    <?php if ($unread_count > 0): ?>
                        <?php foreach($unread_notices as $n): ?>
                            <div class="notification-item">
                                <div style="flex: 1;">
                                    <div class="notification-title"><?php echo htmlspecialchars($n['title']); ?></div>
                                    <div class="notification-content"><?php echo htmlspecialchars($n['content']); ?></div>
                                </div>
                                <a href="<?php echo $path_prefix; ?>modules/notices/dismiss.php?id=<?php echo $n['id']; ?>" class="dismiss-icon" title="Dismiss">
                                    <i class="fa-solid fa-circle-check"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                        <div style="text-align: center; padding: 8px;">
                            <a href="<?php echo $path_prefix; ?>modules/notices/view.php" style="font-size: 11px; color: #3498db; text-decoration: none;">View All Notices</a>
                        </div>
                    <?php else: ?>
                        <div style="padding: 30px; text-align: center; color: #999; font-size: 13px;">No new notifications ✨</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
        </div>
    </div>
</div>

<div class="container">
