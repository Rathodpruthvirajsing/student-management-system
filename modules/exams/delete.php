<?php
include "../../auth/session.php";
include "../../config/db.php";

$id = $_GET['id'];
$sql = "DELETE FROM exams WHERE id='$id'";

if (mysqli_query($conn, $sql)) {
    header("Location: create.php?msg=Exam deleted successfully");
} else {
    header("Location: create.php?msg=Error deleting exam");
}
exit();
?>
