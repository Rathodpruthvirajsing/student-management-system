<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = $_POST['teacher_id'];
    $course_id = $_POST['course_id'];
    $subject_id = $_POST['subject_id'];
    
    $check = mysqli_query($conn, "SELECT id FROM teacher_assignments WHERE teacher_id=$teacher_id AND course_id=$course_id AND subject_id=$subject_id");
    if (mysqli_num_rows($check) > 0) {
        $msg = "Teacher already assigned to this subject in the selected class.";
    } else {
        $sql = "INSERT INTO teacher_assignments (teacher_id, course_id, subject_id) VALUES ($teacher_id, $course_id, $subject_id)";
        if (mysqli_query($conn, $sql)) $msg = "Assignment successful!";
        else $msg = "Error: " . mysqli_error($conn);
    }
}

$teachers = mysqli_fetch_all(mysqli_query($conn, "SELECT id, name FROM teachers"), MYSQLI_ASSOC);
$courses = mysqli_fetch_all(mysqli_query($conn, "SELECT id, course_name FROM courses"), MYSQLI_ASSOC);
$subjects = mysqli_fetch_all(mysqli_query($conn, "SELECT id, subject_name FROM subjects"), MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>🔗 Assign Teacher to Class</h2>
    </div>

    <?php if ($msg) echo "<div class='alert-info'>$msg</div>"; ?>

    <form method="POST" class="form-container" style="max-width: 600px; background: white; padding: 20px; border-radius: 8px;">
        <div class="form-group">
            <label>Select Teacher</label>
            <select name="teacher_id" required>
                <?php foreach ($teachers as $t) echo "<option value='{$t['id']}'>{$t['name']}</option>"; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Select Class (Course)</label>
            <select name="course_id" required>
                <?php foreach ($courses as $c) echo "<option value='{$c['id']}'>{$c['course_name']}</option>"; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Select Subject</label>
            <select name="subject_id" required>
                <?php foreach ($subjects as $s) echo "<option value='{$s['id']}'>{$s['subject_name']}</option>"; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-add">Assign Teacher</button>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
