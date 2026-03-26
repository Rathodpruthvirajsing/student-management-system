<?php
include "../../auth/session.php";
include "../../config/db.php";

if ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin') {
    header("Location: ../../home.php?error=Unauthorized+access");
    exit();
}

$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Check permission
if ($_SESSION['role'] === 'teacher') {
    $check_sql = "SELECT id FROM quizzes WHERE id = $quiz_id AND teacher_id = $user_id";
    if (mysqli_num_rows(mysqli_query($conn, $check_sql)) == 0) {
        header("Location: teacher_view.php?error=Access Denied!");
        exit();
    }
}

$sql = "DELETE FROM quizzes WHERE id = $quiz_id";
if (mysqli_query($conn, $sql)) {
    header("Location: teacher_view.php?msg=" . urlencode("AI Quiz and all corresponding answers wiped successfully."));
} else {
    echo "Error deleting quiz: " . mysqli_error($conn);
}
?>
