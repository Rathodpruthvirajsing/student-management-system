<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['user_id'])) {
    exit();
}

$user_id = intval($_SESSION['user_id']);
$other_id = isset($_GET['other_id']) ? intval($_GET['other_id']) : 0;

if ($other_id > 0) {
    // Mark as read for the user looking at the messages
    $update_sql = "UPDATE messages SET is_read = 1 WHERE receiver_id = $user_id AND sender_id = $other_id";
    mysqli_query($conn, $update_sql);

    // Fetch message history between these two users
    $sql = "SELECT m.*, u.name as sender_name 
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE (m.sender_id = $user_id AND m.receiver_id = $other_id) 
               OR (m.sender_id = $other_id AND m.receiver_id = $user_id)
            ORDER BY m.created_at ASC";
    
    $result = mysqli_query($conn, $sql);
    
    $messages = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = [
            'id' => $row['id'],
            'is_mine' => $row['sender_id'] == $user_id,
            'sender_name' => $row['sender_name'],
            'message' => $row['message'],
            'time' => date('h:i A', strtotime($row['created_at']))
        ];
    }

    echo json_encode($messages);
}
?>
