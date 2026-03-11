<?php
include "../../auth/session.php";
include "../../config/db.php";

$id = $_GET['id'];
$sql = "DELETE FROM fee_payments WHERE id='$id'";

if (mysqli_query($conn, $sql)) {
    header("Location: payment.php?msg=Payment deleted successfully");
} else {
    header("Location: payment.php?msg=Error deleting payment");
}
exit();
?>
