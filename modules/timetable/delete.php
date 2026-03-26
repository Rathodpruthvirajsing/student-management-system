<?php
include "../../auth/session.php";
include "../../config/db.php";

// Only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "DELETE FROM timetables WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: view.php?msg=Schedule deleted successfully");
    } else {
        header("Location: view.php?error=Error deleting schedule: " . mysqli_error($conn));
    }
} else {
    header("Location: view.php");
}
exit();
?>
