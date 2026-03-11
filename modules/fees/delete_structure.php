<?php
include "../../auth/session.php";
include "../../config/db.php";

$id = $_GET['id'];
$sql = "DELETE FROM fee_structure WHERE id='$id'";

if (mysqli_query($conn, $sql)) {
    header("Location: structure.php?msg=Fee structure deleted successfully");
} else {
    header("Location: structure.php?error=Error deleting fee structure");
}
exit();
?>
