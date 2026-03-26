<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

$error = '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    header("Location: view.php?error=Invalid schedule ID");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = intval($_POST['course_id']);
    $day_of_week = trim($_POST['day_of_week']);
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $subject = trim($_POST['subject']);
    $room_number = trim($_POST['room_number']);
    $teacher_name = trim($_POST['teacher_name']);

    if (empty($course_id) || empty($day_of_week) || empty($start_time) || empty($end_time) || empty($subject) || empty($room_number)) {
        $error = "Please fill all required fields";
    } else {
        $sql = "UPDATE timetables SET 
                course_id='$course_id', 
                day_of_week='$day_of_week', 
                start_time='$start_time', 
                end_time='$end_time', 
                subject='$subject', 
                room_number='$room_number', 
                teacher_name='$teacher_name'
                WHERE id=$id";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: view.php?msg=Schedule updated successfully");
            exit();
        } else {
            $error = "Error updating schedule: " . mysqli_error($conn);
        }
    }
}

// Fetch current values before rendering form
$schedule_query = mysqli_query($conn, "SELECT * FROM timetables WHERE id=$id");
$schedule = mysqli_fetch_assoc($schedule_query);

if (!$schedule) {
    header("Location: view.php?error=Schedule not found");
    exit();
}

// Fetch courses for dropdown
$courses_sql = "SELECT * FROM courses ORDER BY course_name ASC";
$courses_result = mysqli_query($conn, $courses_sql);
$courses = mysqli_fetch_all($courses_result, MYSQLI_ASSOC);

// Now include header and sidebar after processing logic
include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <h2>Edit Class Schedule</h2>
    
    <?php if ($error) echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #f5c6cb;">' . $error . '</div>'; ?>

    <form method="POST" class="form-container" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 600px;">
        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Course *</label>
            <select name="course_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">Select Course</option>
                <?php foreach ($courses as $c) { 
                    $selected = ($c['id'] == $schedule['course_id']) ? 'selected' : '';
                ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($c['course_name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Day of Week *</label>
            <select name="day_of_week" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">Select Day</option>
                <?php 
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                foreach ($days as $d) {
                    $selected = ($d == $schedule['day_of_week']) ? 'selected' : '';
                    echo "<option value='$d' $selected>$d</option>";
                }
                ?>
            </select>
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div class="form-group" style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Start Time *</label>
                <input type="time" name="start_time" value="<?php echo htmlspecialchars($schedule['start_time']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div class="form-group" style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-weight: 500;">End Time *</label>
                <input type="time" name="end_time" value="<?php echo htmlspecialchars($schedule['end_time']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Subject *</label>
            <input type="text" name="subject" value="<?php echo htmlspecialchars($schedule['subject']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Lab *</label>
            <input type="text" name="room_number" value="<?php echo htmlspecialchars($schedule['room_number']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div class="form-group" style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Faculties</label>
            <input type="text" name="teacher_name" value="<?php echo htmlspecialchars($schedule['teacher_name']); ?>" placeholder="Leave blank if unknown" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div class="form-actions">
            <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">Update Schedule</button>
            <a href="view.php" style="background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-size: 16px; margin-left: 10px;">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
