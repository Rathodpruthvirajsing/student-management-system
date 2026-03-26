<?php
require_once 'config/db.php';

// New table SQL
$sql = "CREATE TABLE IF NOT EXISTS notices (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    expire_date DATE DEFAULT NULL,
    posted_by INT(11) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    PRIMARY KEY (id),
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if (mysqli_query($conn, $sql)) {
    echo "Notice board table created successfully!";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
?>
