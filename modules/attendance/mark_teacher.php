<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_id = $_POST['teacher_id'];
    $date = $_POST['attendance_date'];
    $status = $_POST['status'];
    
    $check = mysqli_query($conn, "SELECT id FROM teacher_attendance WHERE teacher_id=$teacher_id AND attendance_date='$date'");
    if (mysqli_num_rows($check) > 0) {
        $sql = "UPDATE teacher_attendance SET status='$status' WHERE teacher_id=$teacher_id AND attendance_date='$date'";
    } else {
        $sql = "INSERT INTO teacher_attendance (teacher_id, attendance_date, status) VALUES ($teacher_id, '$date', '$status')";
    }
    
    if (mysqli_query($conn, $sql)) $msg = "Attendance marked successfully!";
    else $msg = "Error: " . mysqli_error($conn);
}

$teachers = mysqli_fetch_all(mysqli_query($conn, "SELECT id, name FROM teachers"), MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>🧑‍🏫 Mark Teacher Attendance</h2>
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
            <label>Date</label>
            <input type="date" name="attendance_date" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Late">Late</option>
            </select>
        </div>
        <button type="submit" class="btn btn-add">Save Attendance</button>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
