<?php
include "../../auth/session.php";
include "../../config/db.php";

if ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

// Get teacher details
$teacher_res = mysqli_query($conn, "SELECT id, course_id FROM teachers WHERE email='".mysqli_real_escape_string($conn, $_SESSION['user_email'])."'");
$teacher = mysqli_fetch_assoc($teacher_res);
$course_id = $teacher['course_id'] ?? null;

// Fetch Timetable for this course
$timetable = [];
if ($course_id) {
    $sql = "SELECT t.* FROM timetables t 
            WHERE t.course_id = $course_id 
            ORDER BY FIELD(t.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), t.start_time ASC";
    $timetable = mysqli_fetch_all(mysqli_query($conn, $sql), MYSQLI_ASSOC);
}

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
?>

<div class="content">
    <div class="header-section">
        <h2>🗓️ My Class Schedule</h2>
        <p>Assigned Course ID: <?php echo $course_id ?: 'N/A'; ?></p>
    </div>

    <div class="timetable-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
        <?php foreach ($days as $day): ?>
            <div class="day-card" style="background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3 style="border-bottom: 2px solid #667eea; padding-bottom: 5px; margin-bottom: 10px; color: #764ba2;"><?php echo $day; ?></h3>
                <?php 
                $found = false;
                foreach ($timetable as $slot) {
                    if ($slot['day_of_week'] === $day) {
                        $found = true;
                        echo "<div style='margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-left: 4px solid #764ba2;'>";
                        echo "<strong>".date('H:i', strtotime($slot['start_time']))." - ".date('H:i', strtotime($slot['end_time']))."</strong><br>";
                        echo "<span>".htmlspecialchars($slot['subject'] ?? 'Subject')."</span><br>";
                        echo "<small style='color: #666;'>Room: ".htmlspecialchars($slot['room_number'] ?? 'N/A')."</small>";
                        echo "</div>";
                    }
                }
                if (!$found) echo "<p style='color: #ccc; font-style: italic;'>No classes scheduled</p>";
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
