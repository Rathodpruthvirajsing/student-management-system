<?php
include "../auth/session.php";
include "../config/db.php";

// Check if student
if ($_SESSION['role'] !== 'student') {
    header("Location: ../dashboard.php");
    exit();
}

// Get student info
$user_id = $_SESSION['user_id'];
$student_query = "SELECT id, email, name, enrollment_no, course_id FROM students WHERE email = (SELECT email FROM users WHERE id='$user_id')";
$student_result = mysqli_query($conn, $student_query);
$student = mysqli_fetch_assoc($student_result);

// Check if student exists
if (!$student) {
    header("Location: ../auth/logout.php");
    exit();
}

$student_id = $student['id'];

// Get course name
$course_query = "SELECT course_name FROM courses WHERE id='" . intval($student['course_id']) . "'";
$course_result = mysqli_query($conn, $course_query);
$course = mysqli_fetch_assoc($course_result);
$course_name = $course['course_name'] ?? 'N/A';

// Get attendance records
$sql = "SELECT a.attendance_date, a.status, c.course_name 
        FROM attendance a 
        JOIN courses c ON a.course_id = c.id 
        WHERE a.student_id='$student_id'
        ORDER BY a.attendance_date ASC";
$result = mysqli_query($conn, $sql);
$attendance_records = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Calculate stats
$total_classes = count($attendance_records);
$present_count = 0;
foreach ($attendance_records as $record) {
    if ($record['status'] === 'Present') {
        $present_count++;
    }
}
$attendance_percentage = $total_classes > 0 ? round(($present_count / $total_classes) * 100, 2) : 0;

// Generate HTML for PDF
$html = '
<html>
<head>
    <title>Attendance Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #333; }
        .header p { margin: 5px 0; color: #666; }
        .info-box { background: #f5f5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .info-box p { margin: 5px 0; }
        .stats { display: flex; justify-content: space-around; margin: 20px 0; padding: 15px; background: #e8f4f8; border-radius: 5px; }
        .stat-item { text-align: center; }
        .stat-item h3 { margin: 0; color: #667eea; }
        .stat-item p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #667eea; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
        .present { color: #28a745; font-weight: bold; }
        .absent { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Attendance Report</h1>
        <p>Student Management System</p>
    </div>
    
    <div class="info-box">
        <p><strong>Student Name:</strong> ' . htmlspecialchars($student['name']) . '</p>
        <p><strong>Enrollment No:</strong> ' . htmlspecialchars($student['enrollment_no']) . '</p>
        <p><strong>Email:</strong> ' . htmlspecialchars($student['email']) . '</p>
        <p><strong>Course:</strong> ' . htmlspecialchars($course_name) . '</p>
        <p><strong>Report Generated:</strong> ' . date('d-M-Y H:i:s') . '</p>
    </div>
    
    <div class="stats">
        <div class="stat-item">
            <h3>' . $total_classes . '</h3>
            <p>Total Classes</p>
        </div>
        <div class="stat-item">
            <h3>' . $present_count . '</h3>
            <p>Days Present</p>
        </div>
        <div class="stat-item">
            <h3>' . ($total_classes - $present_count) . '</h3>
            <p>Days Absent</p>
        </div>
        <div class="stat-item">
            <h3>' . $attendance_percentage . '%</h3>
            <p>Attendance %</p>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Course</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

foreach ($attendance_records as $record) {
    $status_class = $record['status'] === 'Present' ? 'present' : 'absent';
    $html .= '<tr>
                <td>' . date('d-M-Y', strtotime($record['attendance_date'])) . '</td>
                <td>' . htmlspecialchars($record['course_name']) . '</td>
                <td><span class="' . $status_class . '">' . $record['status'] . '</span></td>
            </tr>';
}

$html .= '
        </tbody>
    </table>
    
    <div class="footer">
        <p>This is an auto-generated report from Student Management System</p>
        <p>&copy; 2026 Student Management System. All rights reserved.</p>
    </div>
</body>
</html>';

// Generate PDF filename
$filename = 'Attendance_Report_' . $student['enrollment_no'] . '_' . date('d-m-Y') . '.html';

// Output as downloadable file
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
echo $html;
?>
