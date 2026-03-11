<?php
/**
 * Password Reset Tool
 * Use this to reset user passwords easily
 */
include "config/db.php";

$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validation
    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        // Check if user exists
        $email = mysqli_real_escape_string($conn, $email);
        $query = "SELECT id FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $hashed_password = mysqli_real_escape_string($conn, $hashed_password);
            
            // Update password
            $update_query = "UPDATE users SET password='$hashed_password' WHERE email='$email'";
            if (mysqli_query($conn, $update_query)) {
                $message = "✓ Password reset successfully for $email";
            } else {
                $error = "Error updating password: " . mysqli_error($conn);
            }
        } else {
            $error = "User not found with this email";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Student Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .reset-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .reset-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .reset-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .reset-container input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .reset-container button {
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .reset-container button:hover {
            background: #0056b3;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .warning {
            background: #fff3cd;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            color: #856404;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="reset-container">
    <h2>🔐 Reset User Password</h2>
    
    <div class="warning">
        <strong>⚠️ Note:</strong> This tool is for admin use only. Delete this file after resetting passwords for security.
    </div>
    
    <?php if ($message): ?>
        <div class="alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="email" name="email" placeholder="Enter user email" required>
        <input type="password" name="password" placeholder="New password (min 6 chars)" required>
        <input type="password" name="confirm_password" placeholder="Confirm password" required>
        <button type="submit">Reset Password</button>
    </form>
    
    <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
    
    <div style="text-align: center; margin-top: 15px;">
        <a href="index.php" style="color: #007bff; text-decoration: none;">← Back to Login</a>
    </div>
</div>

</body>
</html>
