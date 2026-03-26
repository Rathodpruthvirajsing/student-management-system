<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';

// Fetch existing data
$res = mysqli_query($conn, "SELECT * FROM assignments WHERE id=$id");
$a = mysqli_fetch_assoc($res);

if (!$a) {
    header("Location: view.php?error=Assignment not found");
    exit();
}

$courses_res = mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name ASC");
$courses = mysqli_fetch_all($courses_res, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $course_id = intval($_POST['course_id']);
    $due_date = $_POST['due_date'];

    $db_path = $a['file_path']; // Keep old file by default

    // If new file uploaded
    if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'png'];
        $filename = $_FILES['assignment_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . "." . $ext;
            $dir = "../../uploads/assignments/";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $upload_path = $dir . $new_filename;
            
            if (@move_uploaded_file($_FILES['assignment_file']['tmp_name'], $upload_path)) {
                // Delete old file
                if (file_exists("../../" . $a['file_path'])) unlink("../../" . $a['file_path']);
                $db_path = "uploads/assignments/" . $new_filename;
            }
        }
    }

    $sql = "UPDATE assignments SET title='$title', description='$description', course_id=$course_id, file_path='$db_path', due_date='$due_date' WHERE id=$id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: view.php?msg=Assignment updated successfully");
        exit();
    } else {
        $error = "DB Error: " . mysqli_error($conn);
    }
}

include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <h2>Edit Assignment</h2>

    <?php if ($error) echo '<div class="alert-error">'.htmlspecialchars($error).'</div>'; ?>

    <form method="POST" enctype="multipart/form-data" class="form-container">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" required value="<?php echo htmlspecialchars($a['title']); ?>">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($a['description']); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Course *</label>
                <select name="course_id" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $c) { ?>
                        <option value="<?php echo $c['id']; ?>" <?php if($c['id'] == $a['course_id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['course_name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Due Date *</label>
                <input type="date" name="due_date" required value="<?php echo $a['due_date']; ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Assignment File (Leave empty to keep existing file)</label>
            <input type="file" name="assignment_file">
            <p style="font-size: 12px; color: #666; margin-top: 5px;">Current: <?php echo basename($a['file_path']); ?></p>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Update Assignment</button>
            <a href="view.php" class="btn btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
