<?php
include "config/db.php";
$res = $conn->query("SELECT email, role, password FROM users LIMIT 10");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        echo $row['email'] . ' | ' . $row['role'] . " | " . substr($row['password'], 0, 10) . "...\n";
    }
} else {
    echo "Query failed: " . $conn->error;
}
