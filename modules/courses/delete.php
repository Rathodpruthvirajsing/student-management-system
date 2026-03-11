<?php
include "../../auth/session.php";
include "../../config/db.php";

$id = $_GET['id'];
$sql = "DELETE FROM courses WHERE id='$id'";

if (mysqli_query($conn, $sql)) {
    header("Location: view.php?msg=Course deleted successfully");
} else {
    header("Location: view.php?msg=Error deleting course");
}
exit();
?>
