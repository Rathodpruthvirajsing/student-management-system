<?php
include "../config/db.php";

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validation
    if (empty($email) || empty($password)) {
        header("Location: ../index.php?error=Email and password required");
        exit();
    }

    // Prevent SQL Injection
    $email_escaped = mysqli_real_escape_string($conn, $email);

    $query  = "SELECT * FROM users WHERE email='$email_escaped' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Support both bcrypt-hashed AND plain-text passwords
        $password_ok = false;
        if (strpos($user['password'], '$2y$') === 0 || strpos($user['password'], '$2a$') === 0) {
            // Hashed password
            $password_ok = password_verify($password, $user['password']);
        } else {
            // Plain-text password (legacy)
            $password_ok = ($password === $user['password']);
        }

        if ($password_ok) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id']       = $user['id'];
            $_SESSION['user_name']     = $user['name'];
            $_SESSION['user_email']    = $user['email'];
            $_SESSION['role']          = $user['role'];
            $_SESSION['last_activity'] = time();

            // Debug log
            $log = date('c') . " LOGIN SUCCESS: user_id=" . $user['id']
                 . "; role=" . $user['role']
                 . "; ip=" . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n";
            @file_put_contents(__DIR__ . "/../logs/login_debug.log", $log, FILE_APPEND);

            // Redirect based on role
            switch ($user['role']) {
                case 'student':
                    header("Location: ../student_dashboard.php");
                    break;
                case 'teacher':
                    header("Location: ../modules/teachers/dashboard.php");
                    break;
                case 'parent':
                    header("Location: ../modules/parents/dashboard.php");
                    break;
                case 'admin':
                default:
                    header("Location: ../dashboard.php");
                    break;
            }
            exit();
        } else {
            $log = date('c') . " LOGIN FAIL (wrong password): email=$email\n";
            @file_put_contents(__DIR__ . "/../logs/login_debug.log", $log, FILE_APPEND);
            header("Location: ../index.php?error=Invalid+credentials");
            exit();
        }
    } else {
        $log = date('c') . " LOGIN FAIL (user not found): email=$email\n";
        @file_put_contents(__DIR__ . "/../logs/login_debug.log", $log, FILE_APPEND);
        header("Location: ../index.php?error=No+account+found+with+that+email");
        exit();
    }
}
// If accessed directly without POST, redirect to login page
header("Location: ../index.php");
exit();
?>
