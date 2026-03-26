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
    $sql = "DELETE FROM notices WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: view.php?msg=Notice deleted successfully");
    } else {
        header("Location: view.php?error=Error deleting notice: " . mysqli_error($conn));
    }
} else {
    header("Location: view.php");
}
exit();
?>
