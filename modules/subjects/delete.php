<?php
include "../../auth/session.php";
include "../../config/db.php";
if ($_SESSION['role'] !== 'admin') { header("Location: ../../index.php"); exit(); }
$id = intval($_GET['id'] ?? 0);
if ($id) {
    if (mysqli_query($conn, "DELETE FROM subjects WHERE id=$id"))
        header("Location: view.php?msg=Subject deleted successfully");
    else header("Location: view.php?msg=Error deleting subject");
} else header("Location: view.php");
exit();
?>
