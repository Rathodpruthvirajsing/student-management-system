<?php
/**
 * Helper function to check attendance percentage and send email notifications
 */
function checkAndNotifyAttendance($student_id, $conn) {
    // 1. Calculate Attendance Percentage
    $sql = "SELECT 
                COUNT(*) as total_classes,
                SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present_count
            FROM attendance 
            WHERE student_id = '$student_id'";
    
    $res = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($res);
    
    if ($data['total_classes'] == 0) {
        return;
    }

    $percentage = ($data['present_count'] / $data['total_classes']) * 100;
    
    // 2. Fetch Student Email and Name
    $user_sql = "SELECT name, email FROM students WHERE id = '$student_id'";
    $user_res = mysqli_query($conn, $user_sql);
    $student = mysqli_fetch_assoc($user_res);
    
    if (!$student || empty($student['email'])) {
        return;
    }

    $to = $student['email'];
    $name = $student['name'];
    $subject = "Attendance Alert - Student Management System";
    $message = "";
    $headers = "From: no-reply@sms-system.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    // 3. Determine Message based on Thresholds
    if ($percentage <= 40) {
        $message = "
            <h2>⚠️ DANGER: Extremely Low Attendance</h2>
            <p>Dear <strong>$name</strong>,</p>
            <p>Your attendance has dropped to <strong>" . round($percentage, 2) . "%</strong>.</p>
            <p style='color: red; font-weight: bold;'>This is a CRITICAL WARNING. Your attendance is dangerously low. Please meet the administrator immediately to avoid academic penalties.</p>
            <hr>
            <p>Student Management System</p>";
    } elseif ($percentage <= 50) {
        $message = "
            <h2>📉 Warning: Attendance Going Low</h2>
            <p>Dear <strong>$name</strong>,</p>
            <p>Your current attendance is <strong>" . round($percentage, 2) . "%</strong>.</p>
            <p>We have noticed that your attendance is consistently dropping. Please make sure to attend your upcoming classes regularly.</p>
            <hr>
            <p>Student Management System</p>";
    } elseif ($percentage <= 60) {
        $message = "
            <h2>ℹ️ Attendance Notification</h2>
            <p>Dear <strong>$name</strong>,</p>
            <p>Your current attendance is <strong>" . round($percentage, 2) . "%</strong>.</p>
            <p>Please focus on maintaining your attendance to stay above the required threshold.</p>
            <hr>
            <p>Student Management System</p>";
    }

    // 4. Send Email & Log
    if (!empty($message)) {
        $success = @mail($to, $subject, $message, $headers);
        
        $log_msg = date('Y-m-d H:i:s') . " - Notification trigger for $to (Attendance: ".round($percentage, 2)."%). Mail success: " . ($success ? "YES" : "NO") . "\n";
        @file_put_contents(__DIR__ . "/../../logs/attendance_notifications.log", $log_msg, FILE_APPEND);
    }
}
?>
