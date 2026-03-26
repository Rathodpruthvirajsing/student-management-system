<?php
include "../../auth/session.php";
include "../../config/db.php";

if ($_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../home.php");
    exit();
}

$leave_id = intval($_POST['leave_id']);
$action = $_POST['action'];

if ($action == 'approve') {
    $status = 'Approved';
} elseif ($action == 'reject') {
    $status = 'Rejected';
} else {
    header("Location: admin.php?error=Invalid action");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Fetch leave info
$sql = "SELECT * FROM leaves WHERE id = $leave_id AND status = 'Pending'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $leave = mysqli_fetch_assoc($result);
    $user_id = $leave['user_id'];
    $role = $leave['role'];
    $start_date = $leave['start_date'];
    $end_date = $leave['end_date'];

    // Update leave status
    $update_sql = "UPDATE leaves SET status = '$status', reviewed_by = $admin_id WHERE id = $leave_id";
    mysqli_query($conn, $update_sql);

    // If approved, automatically insert 'Leave' into the attendance tables
    if ($status === 'Approved') {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $end->modify('+1 day'); // Ensure end_date is inclusive
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);

        if ($role === 'student') {
            // Find student ID and Course
            $s_res = mysqli_query($conn, "SELECT s.id, s.course_id FROM students s JOIN users u ON s.email = u.email WHERE u.id = $user_id");
            if ($s_row = mysqli_fetch_assoc($s_res)) {
                $student_id = $s_row['id'];
                $course_id = $s_row['course_id'];

                foreach ($period as $dt) {
                    $d = $dt->format("Y-m-d");
                    // Insert or overwrite attendance record
                    $check = mysqli_query($conn, "SELECT id FROM attendance WHERE student_id = $student_id AND course_id = $course_id AND attendance_date = '$d'");
                    if (mysqli_num_rows($check) > 0) {
                        mysqli_query($conn, "UPDATE attendance SET status = 'Leave', marked_by = NULL WHERE student_id = $student_id AND course_id = $course_id AND attendance_date = '$d'");
                    } else {
                        mysqli_query($conn, "INSERT INTO attendance (student_id, course_id, attendance_date, status, marked_by) 
                                    VALUES ($student_id, $course_id, '$d', 'Leave', NULL)");
                    }
                }
            }
        } elseif ($role === 'teacher') {
            // Find teacher ID from users table (Wait, is teacher table tracking ID or uses user_id?
            // "SELECT id FROM teachers where email = ..."
            $t_res = mysqli_query($conn, "SELECT t.id FROM teachers t JOIN users u ON t.email = u.email WHERE u.id = $user_id");
            if ($t_row = mysqli_fetch_assoc($t_res)) {
                $teacher_id = $t_row['id'];
                
                foreach ($period as $dt) {
                    $d = $dt->format("Y-m-d");
                    $check = mysqli_query($conn, "SELECT id FROM teacher_attendance WHERE teacher_id = $teacher_id AND attendance_date = '$d'");
                    if (mysqli_num_rows($check) > 0) {
                        mysqli_query($conn, "UPDATE teacher_attendance SET status = 'Leave' WHERE teacher_id = $teacher_id AND attendance_date = '$d'");
                    } else {
                        mysqli_query($conn, "INSERT INTO teacher_attendance (teacher_id, attendance_date, status) VALUES ($teacher_id, '$d', 'Leave')");
                    }
                }
            }
        }
    }
    
    header("Location: admin.php?msg=" . urlencode("Leave application " . strtolower($status) . " successfully!"));
    exit();
} else {
    header("Location: admin.php?error=Leave record not found or already processed.");
    exit();
}
?>
