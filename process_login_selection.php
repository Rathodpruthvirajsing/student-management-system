<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['role'])) {
    $role = $_POST['role'];
    
    if (in_array($role, ['student', 'admin', 'teacher', 'parent'])) {
        header("Location: index.php?type=" . urlencode($role));
        exit();
    }
}

// If no role selected, go back
header("Location: login_selection.php");
exit();
?>
