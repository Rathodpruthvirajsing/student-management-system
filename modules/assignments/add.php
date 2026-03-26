<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

$error = '';
$courses_res = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");
$courses = mysqli_fetch_all($courses_res, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $course_id = intval($_POST['course_id']);
    $due_date = $_POST['due_date'];
    $teacher_id = $_SESSION['user_id'];

    if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'png'];
        $filename = $_FILES['assignment_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . "." . $ext;
            $dir = "../../uploads/assignments/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            
            $upload_path = $dir . $new_filename;
            $db_path = "uploads/assignments/" . $new_filename;

            if (@move_uploaded_file($_FILES['assignment_file']['tmp_name'], $upload_path)) {
                $sql = "INSERT INTO assignments (title, description, course_id, file_path, due_date, created_by) 
                        VALUES ('$title', '$description', $course_id, '$db_path', '$due_date', $teacher_id)";
                
                if (mysqli_query($conn, $sql)) {
                    header("Location: view.php?msg=Assignment added successfully");
                    exit();
                } else {
                    $error = "DB Error: " . mysqli_error($conn);
                }
            } else {
                $error = "Failed to upload file.";
            }
        } else {
            $error = "Invalid file type. Allowed: PDF, DOC, DOCX, JPG, PNG";
        }
    } else {
        $error = "Assignment file is required.";
    }
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <h2>Post New Assignment</h2>

    <?php if ($error) echo '<div class="alert-error">'.htmlspecialchars($error).'</div>'; ?>

    <form method="POST" enctype="multipart/form-data" class="form-container">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" required placeholder="e.g. Mathematics Week 1 HW">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" placeholder="Instructions for students..."></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Course *</label>
                <select name="course_id" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $c) { ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['course_name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Due Date *</label>
                <input type="date" name="due_date" required value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Assignment File * (PDF, DOC, DOCX, Images)</label>
            <input type="file" name="assignment_file" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Post Assignment</button>
            <a href="view.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
