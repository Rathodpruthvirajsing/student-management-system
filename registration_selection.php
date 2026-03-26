<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Choose Your Role</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        
        .registration-selector {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 700px;
            width: 100%;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 50px 30px;
        }
        
        .registration-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .registration-option {
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            padding: 30px;
            border: 3px solid #eee;
            border-radius: 8px;
            background: #f8f9fa;
            text-decoration: none;
            color: inherit;
        }
        
        .registration-option:hover {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        }
        
        .registration-option.active {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
        }
        
        .option-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .option-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        
        .option-desc {
            font-size: 13px;
            color: #666;
            line-height: 1.5;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        
        .btn {
            padding: 12px 40px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-proceed {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-proceed:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-proceed:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-back {
            background: #e0e0e0;
            color: #333;
        }
        
        .btn-back:hover {
            background: #d0d0d0;
        }
        
        .info {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #856404;
        }
        
        @media (max-width: 600px) {
            .header h1 {
                font-size: 24px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .registration-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="registration-selector">
    <!-- Header -->
    <div class="header">
        <h1>📝 Create Your Account</h1>
        <p>Select your registration type</p>
    </div>
    
    <!-- Content -->
    <div class="content">
        <div class="info">
            ℹ️ Select whether you want to register as a <strong>Student</strong>, <strong>Teacher</strong>, <strong>Parent</strong>, or <strong>Admin</strong>.
        </div>
        
        <!-- Registration Options -->
        <form method="POST" action="process_registration_selection.php">
            <div class="registration-options">
                <!-- Admin Registration -->
                <label class="registration-option" onclick="selectRole('admin', this)">
                    <input type="radio" name="role" value="admin" style="display: none;">
                    <div class="option-icon">👨‍💼</div>
                    <div class="option-title">Admin Account</div>
                    <div class="option-desc">Create an administrative account to oversee system resources.</div>
                </label>

                <!-- Student Registration -->
                <label class="registration-option" onclick="selectRole('student', this)">
                    <input type="radio" name="role" value="student" style="display: none;">
                    <div class="option-icon">👨‍🎓</div>
                    <div class="option-title">Student Account</div>
                    <div class="option-desc">Register as a student to enroll in courses and track grades.</div>
                </label>

                <!-- Teacher Registration -->
                <label class="registration-option" onclick="selectRole('teacher', this)">
                    <input type="radio" name="role" value="teacher" style="display: none;">
                    <div class="option-icon">👨‍🏫</div>
                    <div class="option-title">Teacher Account</div>
                    <div class="option-desc">Register as a faculty member to manage classes and students.</div>
                </label>

                <!-- Parent Registration -->
                <label class="registration-option" onclick="selectRole('parent', this)">
                    <input type="radio" name="role" value="parent" style="display: none;">
                    <div class="option-icon">👪</div>
                    <div class="option-title">Parent Account</div>
                    <div class="option-desc">Register as a parent to monitor child's educational progress.</div>
                </label>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="login_selection.php" class="btn btn-back">← Back</a>
                <button type="submit" class="btn btn-proceed" id="proceedBtn" disabled>Continue →</button>
            </div>
        </form>
        
        <!-- Additional Links -->
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <p style="font-size: 14px; color: #666; margin-bottom: 10px;">Already have an account?</p>
            <a href="login_selection.php" style="display: inline-block; background: #667eea; color: white; padding: 10px 30px; border-radius: 4px; text-decoration: none; font-weight: 600;">🔐 Login</a>
        </div>
    </div>
</div>

<script>
    function selectRole(role, element) {
        // Remove active class from all options
        document.querySelectorAll('.registration-option').forEach(opt => {
            opt.classList.remove('active');
        });
        
        // Add active class to selected option
        element.classList.add('active');
        element.querySelector('input[type="radio"]').checked = true;
        
        // Enable proceed button
        document.getElementById('proceedBtn').disabled = false;
    }
    
    // Allow clicking on the option to select
    document.querySelectorAll('.registration-option').forEach(element => {
        element.addEventListener('click', function() {
            selectRole(this.querySelector('input[name="role"]').value, this);
        });
    });
</script>

</body>
</html>
