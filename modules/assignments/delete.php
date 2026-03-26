<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    exit("Unauthorized");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Get file path to delete from disk
    $res = mysqli_query($conn, "SELECT file_path FROM assignments WHERE id=$id");
    $a = mysqli_fetch_assoc($res);
    
    if ($a) {
        $file = "../../" . $a['file_path'];
        if (file_exists($file)) unlink($file);
        
        // Also delete submissions files
        $sub_res = mysqli_query($conn, "SELECT file_path FROM assignment_submissions WHERE assignment_id=$id");
        while($s = mysqli_fetch_assoc($sub_res)) {
            $s_file = "../../" . $s['file_path'];
            if (file_exists($s_file)) unlink($s_file);
        }

        mysqli_query($conn, "DELETE FROM assignments WHERE id=$id");
        header("Location: view.php?msg=Assignment and all submissions deleted");
        exit();
    }
}
header("Location: view.php");
?>
