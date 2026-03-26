<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['user_id'])) {
    exit;
}

$user_id = intval($_SESSION['user_id']);
$notice_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($notice_id > 0) {
    $insert = "INSERT IGNORE INTO notice_reads (user_id, notice_id) VALUES ($user_id, $notice_id)";
    mysqli_query($conn, $insert);
}
echo "ok";
?>
