<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['role'])) {
    $role = $_POST['role'];
    
    if ($role === 'student') {
        // Redirect to student login (which is the same as register or index)
        header("Location: index.php?type=student");
        exit();
    } elseif ($role === 'admin') {
        // Redirect to admin login
        header("Location: index.php?type=admin");
        exit();
    }
}

// If no role selected, go back
header("Location: login_selection.php");
exit();
?>
