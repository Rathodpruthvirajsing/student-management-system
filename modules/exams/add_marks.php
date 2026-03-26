<?php
include "../../auth/session.php";
include "../../config/db.php";

if ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_marks'])) {
    $exam_id = $_POST['exam_id'];
    $student_marks = $_POST['marks']; // Array student_id => marks
    
    foreach ($student_marks as $sid => $m) {
        $sid = intval($sid);
        $m = floatval($m);
        // Check if exists
        $check = mysqli_query($conn, "SELECT id FROM marks WHERE exam_id=$exam_id AND student_id=$sid");
        if (mysqli_num_rows($check) > 0) {
            mysqli_query($conn, "UPDATE marks SET marks_obtained=$m WHERE exam_id=$exam_id AND student_id=$sid");
        } else {
            mysqli_query($conn, "INSERT INTO marks (exam_id, student_id, marks_obtained) VALUES ($exam_id, $sid, $m)");
        }
    }
    $msg = "Marks saved successfully!";
}

// Get teacher's course
$course_id = null;
if ($_SESSION['role'] === 'teacher') {
    $t_res = mysqli_query($conn, "SELECT course_id FROM teachers WHERE email='".mysqli_real_escape_string($conn, $_SESSION['user_email'])."'");
    $course_id = mysqli_fetch_assoc($t_res)['course_id'] ?? null;
}

$exams = mysqli_fetch_all(mysqli_query($conn, "SELECT id, exam_name FROM exams" . ($course_id ? " WHERE course_id=$course_id" : "")), MYSQLI_ASSOC);

$selected_exam_id = isset($_POST['exam_id']) ? $_POST['exam_id'] : (isset($_GET['exam_id']) ? $_GET['exam_id'] : null);
$students = [];
if ($selected_exam_id) {
    $exam_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT course_id FROM exams WHERE id=$selected_exam_id"));
    $ecid = $exam_data['course_id'];
    $sql = "SELECT s.id, s.name, s.enrollment_no, m.marks_obtained 
            FROM students s 
            LEFT JOIN marks m ON s.id = m.student_id AND m.exam_id = $selected_exam_id 
            WHERE s.course_id = $ecid 
            ORDER BY s.name ASC";
    $students = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
}
?>

<div class="content">
    <div class="header-section">
        <h2>📝 Add / Update Exam Marks</h2>
    </div>

    <?php if ($msg) echo "<div class='alert-info'>$msg</div>"; ?>

    <div class="card" style="background: white; padding: 20px; border-radius: 8px;">
        <form method="GET" style="margin-bottom: 20px;">
            <div class="form-group">
                <label>Select Exam</label>
                <select name="exam_id" onchange="this.form.submit()" style="width: 100%; max-width: 400px; padding: 10px; border-radius: 4px; border: 1px solid #ddd;">
                    <option value="">-- Select Exam --</option>
                    <?php foreach ($exams as $e): ?>
                        <option value="<?php echo $e['id']; ?>" <?php echo $selected_exam_id == $e['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['exam_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if ($selected_exam_id && count($students) > 0): ?>
            <form method="POST">
                <input type="hidden" name="exam_id" value="<?php echo $selected_exam_id; ?>">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Enrollment No</th>
                            <th>Marks Obtained</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['name']); ?></td>
                                <td><?php echo htmlspecialchars($s['enrollment_no']); ?></td>
                                <td>
                                    <input type="number" step="0.5" name="marks[<?php echo $s['id']; ?>]" value="<?php echo $s['marks_obtained']; ?>" style="width: 80px; padding: 5px; border: 1px solid #ddd; border-radius: 4px;">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="save_marks" class="btn btn-add" style="margin-top: 20px;">💾 Save Marks</button>
            </form>
        <?php elseif ($selected_exam_id): ?>
            <p style="color: #666; font-style: italic;">No students enrolled in the course associated with this exam.</p>
        <?php endif; ?>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
