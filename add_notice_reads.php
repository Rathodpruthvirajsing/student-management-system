<?php
include "config/db.php";

$sql = "CREATE TABLE IF NOT EXISTS notice_reads (
    user_id INT(11) NOT NULL,
    notice_id INT(11) NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(user_id, notice_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (notice_id) REFERENCES notices(id) ON DELETE CASCADE
)";
if (mysqli_query($conn, $sql)) {
    echo "Notice Reads table established successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
