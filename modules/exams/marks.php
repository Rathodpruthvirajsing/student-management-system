<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$error = '';
$success = '';
$exam_id = $_GET['exam_id'];

// Fetch exam details
$exam_result = mysqli_query($conn, "SELECT * FROM exams WHERE id='$exam_id'");
$exam = mysqli_fetch_assoc($exam_result);

if (!$exam) {
    header("Location: create.php?msg=Exam not found");
    exit();
}

// Fetch students for this course
$students_result = mysqli_query($conn, "SELECT id, name, enrollment_no FROM students WHERE course_id='{$exam['course_id']}' ORDER BY name ASC");
$students = mysqli_fetch_all($students_result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['marks'] as $student_id => $marks) {
        if ($marks !== '') {
            // Check if marks already exist
            $check = mysqli_query($conn, "SELECT id FROM marks WHERE student_id='$student_id' AND exam_id='$exam_id'");
            
            if (mysqli_num_rows($check) > 0) {
                $sql = "UPDATE marks SET marks_obtained='$marks' WHERE student_id='$student_id' AND exam_id='$exam_id'";
            } else {
                $sql = "INSERT INTO marks (student_id, exam_id, marks_obtained) VALUES ('$student_id', '$exam_id', '$marks')";
            }
            
            if (!mysqli_query($conn, $sql)) {
                $error = "Error saving marks: " . mysqli_error($conn);
            }
        }
    }
    
    if (!$error) {
        $success = "Marks saved successfully";
    }
}

// Fetch existing marks
$marks_result = mysqli_query($conn, "SELECT * FROM marks WHERE exam_id='$exam_id'");
$marks_data = mysqli_fetch_all($marks_result, MYSQLI_ASSOC);
$marks_array = [];
foreach ($marks_data as $mark) {
    $marks_array[$mark['student_id']] = $mark['marks_obtained'];
}
?>

<div class="content">
    <h2>Add Marks for <?php echo htmlspecialchars($exam['exam_name']); ?></h2>
    <p><strong>Total Marks:</strong> <?php echo $exam['total_marks']; ?></p>

    <?php if ($error) echo '<div class="alert-error">' . $error . '</div>'; ?>
    <?php if ($success) echo '<div class="alert-success">' . $success . '</div>'; ?>

    <form method="POST" class="form-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Enrollment No</th>
                    <th>Marks Obtained (out of <?php echo $exam['total_marks']; ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['enrollment_no']); ?></td>
                        <td>
                            <input type="number" name="marks[<?php echo $student['id']; ?>]" 
                                   min="0" max="<?php echo $exam['total_marks']; ?>" 
                                   value="<?php echo isset($marks_array[$student['id']]) ? $marks_array[$student['id']] : ''; ?>" 
                                   placeholder="Enter marks">
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="form-actions">
            <button type="submit" class="btn btn-add">Save Marks</button>
            <a href="create.php" class="btn btn-cancel">Back</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
