<?php
include "../../auth/session.php";
include "../../config/db.php";

// Allow student and parent
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'parent')) {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

$course_id = 0;
$course_name = 'Unknown Course';

if ($_SESSION['role'] === 'student') {
    $uid = $_SESSION['user_id'];
    $student_query = "SELECT s.course_id, c.course_name FROM students s 
                      LEFT JOIN courses c ON s.course_id = c.id
                      WHERE s.email = (SELECT email FROM users WHERE id='$uid' LIMIT 1)";
    $student = mysqli_fetch_assoc(mysqli_query($conn, $student_query));
    $course_id = $student['course_id'] ?? 0;
    $course_name = $student['course_name'] ?? 'Unknown Course';
} elseif ($_SESSION['role'] === 'parent') {
    $parent_email = $_SESSION['user_email'];
    $child_res = mysqli_query($conn, "SELECT s.course_id, c.course_name FROM parents p JOIN students s ON p.student_id = s.id LEFT JOIN courses c ON s.course_id = c.id WHERE p.email='$parent_email'");
    $child = mysqli_fetch_assoc($child_res);
    $course_id = $child['course_id'] ?? 0;
    $course_name = $child['course_name'] ?? 'Unknown Course';
}

// Fetch all timetables
$sql = "SELECT t.*, c.course_name FROM timetables t LEFT JOIN courses c ON t.course_id = c.id ORDER BY FIELD(t.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), t.start_time";
$result = mysqli_query($conn, $sql);
$timetables = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Group by day for a nicer student view
$schedule_by_day = [
    'Monday' => [],
    'Tuesday' => [],
    'Wednesday' => [],
    'Thursday' => [],
    'Friday' => [],
    'Saturday' => [],
    'Sunday' => []
];

foreach ($timetables as $t) {
    if (isset($schedule_by_day[$t['day_of_week']])) {
        $schedule_by_day[$t['day_of_week']][] = $t;
    }
}
?>

<div class="content">
    <div class="header-section" style="margin-bottom: 25px;">
        <h2>All Class Schedules</h2>
    </div>

    <?php if (count($timetables) == 0): ?>
        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; border: 1px solid #e9ecef;">
            <p style="color: #6c757d; font-size: 16px;">No schedules found.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($schedule_by_day as $day => $sessions): ?>
                <?php if (count($sessions) > 0): ?>
                <div style="background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;">
                    <div style="background: #667eea; color: white; padding: 10px 20px; font-weight: 600; display: flex; justify-content: space-between;">
                        <span>📅 <?php echo $day; ?></span>
                        <small><?php echo count($sessions); ?> Sessions</small>
                    </div>
                    <div style="padding: 10px;">
                        <table class="table" style="margin: 0; border: none;">
                            <thead>
                                <tr style="background: #f8f9fa; font-size: 12px; color: #7f8c8d;">
                                    <th style="border: none;">Time</th>
                                    <th style="border: none;">Subject / Room</th>
                                    <th style="border: none;">Faculty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions as $s): ?>
                                <tr>
                                    <td style="border: none; width: 30%;">
                                        <div style="font-weight: 600; color: #667eea;"><?php echo date('h:i A', strtotime($s['start_time'])); ?></div>
                                        <div style="font-size: 11px; color: #999;">to <?php echo date('h:i A', strtotime($s['end_time'])); ?></div>
                                    </td>
                                    <td style="border: none;">
                                        <div style="font-weight: 600; color: #2c3e50;"><?php echo htmlspecialchars($s['subject']); ?></div>
                                        <div style="font-size: 11px; color: #7f8c8d;"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($s['room_number']); ?></div>
                                    </td>
                                    <td style="border: none;">
                                        <div style="font-size: 13px; color: #555;"><?php echo htmlspecialchars($s['teacher_name'] ?? 'Faculty'); ?></div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include "../../includes/footer.php"; ?>
