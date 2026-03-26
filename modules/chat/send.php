<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['user_id'])) {
    exit();
}

$sender_id = intval($_SESSION['user_id']);
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
$message = trim($_POST['message']);

if ($receiver_id > 0 && !empty($message)) {
    // Basic protection against XSS
    $clean_msg = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
    // Using prepared statements for security since this is live user input
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $clean_msg);
    if ($stmt->execute()) {
        echo "sent";
    } else {
        echo "error";
    }
}
?>
