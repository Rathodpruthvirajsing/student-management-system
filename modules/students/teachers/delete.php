<?php
include "../../../auth/session.php";
include "../../../config/db.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: view.php?msg=Invalid teacher ID");
    exit();
}

$sql = "DELETE FROM teachers WHERE id=$id";

if (mysqli_query($conn, $sql)) {
    header("Location: view.php?msg=Teacher deleted successfully");
} else {
    header("Location: view.php?msg=Error deleting teacher");
}
exit();
?>
