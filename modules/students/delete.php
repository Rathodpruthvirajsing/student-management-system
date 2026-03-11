<?php
include "../../auth/session.php";
include "../../config/db.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Get student email first
    $get_email = mysqli_query($conn, "SELECT email FROM students WHERE id='$id'");
    if ($get_email && mysqli_num_rows($get_email) > 0) {
        $row = mysqli_fetch_assoc($get_email);
        $email = $row['email'];
        
        // Delete from students
        $query = "DELETE FROM students WHERE id='$id'";
        
        if (mysqli_query($conn, $query)) {
            // Also delete user account if it exists
            if (!empty($email)) {
                $email_safe = mysqli_real_escape_string($conn, $email);
                mysqli_query($conn, "DELETE FROM users WHERE email='$email_safe' AND role='student'");
            }
            header("Location: view.php");
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        header("Location: view.php");
        exit();
    }
}
?>