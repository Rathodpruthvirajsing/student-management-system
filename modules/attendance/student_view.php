<?php
session_start();

// Check if authorized (Student or Parent)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['student', 'parent'])) {
    header("Location: ../../index.php?error=Unauthorized+access");
    exit();
}

include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";

$student_id = null;

if ($_SESSION['role'] === 'student') {
    // Get student info from session
    $user_id = $_SESSION['user_id'];
    $student_query = "SELECT id FROM students WHERE email = (SELECT email FROM users WHERE id='$user_id')";
    $student_result = mysqli_query($conn, $student_query);
    $student = mysqli_fetch_assoc($student_result);
    if (!$student) { header("Location: ../../auth/logout.php"); exit(); }
    $student_id = $student['id'];
} elseif ($_SESSION['role'] === 'parent') {
    // Get child ID linked to parent
    $user_email = $_SESSION['user_email'];
    $parent_query = "SELECT student_id FROM parents WHERE email = '$user_email'";
    $parent_result = mysqli_query($conn, $parent_query);
    $parent = mysqli_fetch_assoc($parent_result);
    if (!$parent || !$parent['student_id']) { 
        echo "<div class='content'><h2>Error</h2><p>No linked student found for this parent account.</p></div>";
        include "../../includes/footer.php";
        exit(); 
    }
    $student_id = $parent['student_id'];
}

// Get attendance records for this student only
$attendance_records = [];
if ($student_id) {
    $sql = "SELECT a.id, a.attendance_date, a.status, c.course_name 
            FROM attendance a 
            JOIN courses c ON a.course_id = c.id 
            WHERE a.student_id='$student_id'
            ORDER BY a.attendance_date DESC";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $attendance_records = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}

// Calculate stats
$total_classes = count($attendance_records);
$present_count = 0;
foreach ($attendance_records as $record) {
    if ($record['status'] === 'Present') {
        $present_count++;
    }
}
$attendance_percentage = $total_classes > 0 ? round(($present_count / $total_classes) * 100, 2) : 0;
?>

<div class="content">
    <div class="header-section">
        <h2>📋 My Attendance</h2>
    </div>

    <!-- Attendance Summary -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <div style="padding: 15px; background: #f0f8ff; border-radius: 8px; border-left: 4px solid #2196F3; text-align: center;">
            <div style="font-size: 24px; font-weight: 600; color: #2196F3;"><?php echo $total_classes; ?></div>
            <div style="font-size: 12px; color: #666;">Total Classes</div>
        </div>
        <div style="padding: 15px; background: #f0fff4; border-radius: 8px; border-left: 4px solid #28a745; text-align: center;">
            <div style="font-size: 24px; font-weight: 600; color: #28a745;"><?php echo $present_count; ?></div>
            <div style="font-size: 12px; color: #666;">Days Present</div>
        </div>
        <div style="padding: 15px; background: #fff5f5; border-radius: 8px; border-left: 4px solid #dc3545; text-align: center;">
            <div style="font-size: 24px; font-weight: 600; color: #dc3545;"><?php echo $total_classes - $present_count; ?></div>
            <div style="font-size: 12px; color: #666;">Days Absent</div>
        </div>
        <div style="padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107; text-align: center;">
            <div style="font-size: 24px; font-weight: 600; color: #ffc107;"><?php echo $attendance_percentage; ?>%</div>
            <div style="font-size: 12px; color: #666;">Attendance %</div>
        </div>
    </div>

    <!-- Attendance Calendar View -->
    <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <h3 style="margin-bottom: 20px; color: #2c3e50; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-calendar-days" style="color: #667eea;"></i> Attendance History (Current Month)
        </h3>
        
        <?php
        $month = date('m');
        $year = date('Y');
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $first_day = date('w', strtotime("$year-$month-01"));
        
        // Map attendance for fast lookup
        $status_map = [];
        foreach ($attendance_records as $r) {
            $d = date('j', strtotime($r['attendance_date']));
            $m = date('m', strtotime($r['attendance_date']));
            $y = date('Y', strtotime($r['attendance_date']));
            if ($m == $month && $y == $year) {
                $status_map[$d] = $r['status'];
            }
        }
        ?>
        
        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; text-align: center;">
            <?php 
            $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            foreach ($days as $day) echo "<div style='font-weight: bold; color: #7f8c8d; font-size: 13px; padding-bottom: 10px;'>$day</div>";
            
            // Empty slots before first day
            for ($i = 0; $i < $first_day; $i++) echo "<div></div>";
            
            // Days of month
            for ($day = 1; $day <= $days_in_month; $day++) {
                $status = $status_map[$day] ?? null;
                $bg = '#f8f9fa';
                $color = '#333';
                $border = 'none';
                
                if ($status === 'Present') { $bg = '#2ecc71'; $color = 'white'; }
                elseif ($status === 'Absent') { $bg = '#e74c3c'; $color = 'white'; }
                
                // Highlight today
                if ($day == date('j')) $border = '2px solid #667eea';
                
                echo "<div style='background: $bg; color: $color; border: $border; padding: 12px 5px; border-radius: 6px; font-weight: 600; font-size: 14px;'>$day</div>";
            }
            ?>
        </div>
        
        <div style="margin-top: 20px; display: flex; gap: 20px; font-size: 12px; color: #666;">
            <div style="display: flex; align-items: center; gap: 5px;"><span style="width: 12px; height: 12px; background: #2ecc71; border-radius: 2px;"></span> Present</div>
            <div style="display: flex; align-items: center; gap: 5px;"><span style="width: 12px; height: 12px; background: #e74c3c; border-radius: 2px;"></span> Absent</div>
            <div style="display: flex; align-items: center; gap: 5px;"><span style="width: 12px; height: 12px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 2px;"></span> No Record</div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Course</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($attendance_records) > 0): ?>
                <?php foreach ($attendance_records as $record): ?>
                    <tr>
                        <td><?php echo date('d-M-Y', strtotime($record['attendance_date'])); ?></td>
                        <td><?php echo htmlspecialchars($record['course_name']); ?></td>
                        <td>
                            <span style="padding: 5px 10px; border-radius: 4px; font-weight: 600; 
                                <?php echo $record['status'] === 'Present' ? 'background: #d4edda; color: #155724;' : 'background: #f8d7da; color: #721c24;'; ?>">
                                <?php echo $record['status']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 30px;">No attendance records found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
