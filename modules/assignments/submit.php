<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

$assignment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

// Check if assignment exists and student hasn't submitted yet
$check_sql = "SELECT * FROM assignments WHERE id = $assignment_id";
$check_res = mysqli_query($conn, $check_sql);
$assignment = mysqli_fetch_assoc($check_res);

if (!$assignment) {
    header("Location: student_view.php?error=Assignment not found");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] == 0) {
        $allowed = ['pdf', 'zip', 'docx', 'jpg', 'png'];
        $filename = $_FILES['submission_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = "SUB_" . $assignment_id . "_" . $user_id . "_" . time() . "." . $ext;
            $dir = "../../uploads/submissions/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            
            $upload_path = $dir . $new_filename;
            $db_path = "uploads/submissions/" . $new_filename;

            if (@move_uploaded_file($_FILES['submission_file']['tmp_name'], $upload_path)) {
                $sql = "INSERT INTO assignment_submissions (assignment_id, student_id, file_path) 
                        VALUES ($assignment_id, $user_id, '$db_path')";
                
                if (mysqli_query($conn, $sql)) {
                    header("Location: student_view.php?msg=Work submitted successfully!");
                    exit();
                } else {
                    $error = "DB Error: " . mysqli_error($conn);
                }
            } else {
                $error = "Failed to upload file.";
            }
        } else {
            $error = "Invalid file type. Allowed: PDF, ZIP, DOCX, Images";
        }
    } else {
        $error = "Please select a file to submit.";
    }
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <h2>Submit Work: <?php echo htmlspecialchars($assignment['title']); ?></h2>

    <?php if (isset($error)) echo '<div class="alert-error">'.htmlspecialchars($error).'</div>'; ?>

    <div class="form-container">
        <p style="margin-bottom: 20px; color: #666; font-size: 14px;">Upload your completed assignment file below. Please ensure your filename is clear.</p>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>File * (PDF, ZIP, DOCX, Images)</label>
                <input type="file" name="submission_file" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-add">Upload Assignment</button>
                <a href="student_view.php" class="btn btn-cancel">Go Back</a>
            </div>
        </form>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
