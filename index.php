<?php
session_start();

// If user is already logged in, redirect to appropriate dashboard
// But allow login if coming from role selection (has type parameter)
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && !isset($_GET['type'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Management System - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-type-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }
        .login-type-badge.admin {
            background: #dc3545;
        }
    </style>
</head>
<body class="login-body">

<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <h2>Student Management System</h2>
            <p>
                <?php 
                $type = $_GET['type'] ?? 'student';
                $role_label = ucfirst($type);
                $badge_class = ($type === 'admin') ? 'admin' : '';
                echo $role_label . ' Login <span class="login-type-badge ' . $badge_class . '">' . strtoupper($type) . '</span>';
                ?>
            </p>
        </div>

        <?php
        if (isset($_GET['error'])) {
            echo '<div class="alert-error">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        if (isset($_GET['msg'])) {
            echo '<div class="alert-success">' . htmlspecialchars($_GET['msg']) . '</div>';
        }
        ?>

        <form action="auth/login.php" method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <!-- <p style="text-align:center; margin-top:15px; font-size:12px;">
            Default Admin: admin@example.com / admin123 
        </p>-->

        <p style="text-align:center; margin-top:15px;">
            <a href="reset_password.php" style="color: #007bff; text-decoration: none; font-size: 12px;">🔐 Reset Password</a>
        </p>

        <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">

        <p style="text-align:center; margin-top:15px; font-size:12px;">
            <a href="login_selection.php" style="color: #667eea; text-decoration: none; font-weight: 600; margin-right: 15px;">← Back to Role Selection</a>
        </p>

        <p style="text-align:center; margin-top:15px; font-size:14px;">
            Don't have an account? <a href="register.php" style="color: #28a745; text-decoration: none; font-weight: 600;">📝 Register here</a>
        </p>
    </div>
</div>

</body>
</html>