<?php
include "../../auth/session.php";
include "../../config/db.php";

if (isset($_GET['id'])) {
    $notice_id = intval($_GET['id']);
    $user_id = intval($_SESSION['user_id']);
    
    // Check if already exists
    $check = mysqli_query($conn, "SELECT id FROM notice_reads WHERE notice_id = $notice_id AND user_id = $user_id");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO notice_reads (notice_id, user_id) VALUES ($notice_id, $user_id)");
    }
}

// Redirect back to where they came from or home
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    header("Location: ../../home.php");
}
exit();
?>
