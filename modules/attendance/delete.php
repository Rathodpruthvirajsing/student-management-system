<?php
include "../../auth/session.php";
include "../../config/db.php";

$id = $_GET['id'];
$sql = "DELETE FROM attendance WHERE id='$id'";

if (mysqli_query($conn, $sql)) {
    header("Location: view.php?msg=Attendance record deleted successfully");
} else {
    header("Location: view.php?msg=Error deleting record");
}
exit();
?>
