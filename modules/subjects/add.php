<?php
include "../../auth/session.php";
include "../../config/db.php";
if ($_SESSION['role'] !== 'admin') { header("Location: ../../index.php"); exit(); }
include "../../includes/header.php";
include "../../includes/sidebar.php";

$msg = '';
$courses = mysqli_fetch_all(mysqli_query($conn, "SELECT id, course_name FROM courses ORDER BY course_name"), MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, trim($_POST['subject_name']));
    $code = mysqli_real_escape_string($conn, trim($_POST['subject_code']));
    $course_id = intval($_POST['course_id']);
    if ($name) {
        $sql = "INSERT INTO subjects (subject_name, subject_code, course_id) VALUES ('$name', '$code', " . ($course_id ?: 'NULL') . ")";
        if (mysqli_query($conn, $sql)) { header("Location: view.php?msg=Subject added successfully"); exit(); }
        else $msg = "Error: " . mysqli_error($conn);
    } else $msg = "Subject name is required.";
}
?>
<div class="content">
    <div class="header-section"><h2>📚 Add New Subject</h2></div>
    <?php if ($msg) echo "<div class='alert-error'>$msg</div>"; ?>
    <form method="POST" class="form-container" style="max-width:500px; background:white; padding:25px; border-radius:8px;">
        <div class="form-group"><label>Subject Name *</label><input type="text" name="subject_name" required placeholder="e.g. Mathematics"></div>
        <div class="form-group"><label>Subject Code</label><input type="text" name="subject_code" placeholder="e.g. MATH101"></div>
        <div class="form-group"><label>Assign to Course</label>
            <select name="course_id"><option value="">-- Select Course --</option>
                <?php foreach ($courses as $c) echo "<option value='{$c['id']}'>{$c['course_name']}</option>"; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-add">Add Subject</button>
        <a href="view.php" class="btn btn-cancel" style="margin-left:10px;">Cancel</a>
    </form>
</div>
<?php include "../../includes/footer.php"; ?>
