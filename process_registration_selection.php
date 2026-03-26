<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['role'])) {
    $role = $_POST['role'];
    
    if (in_array($role, ['student', 'admin', 'teacher', 'parent'])) {
        // Redirect to registration with role parameter
        header("Location: register.php?role=" . urlencode($role));
        exit();
    }
}

// If no role selected, go back
header("Location: registration_selection.php");
exit();
?>
