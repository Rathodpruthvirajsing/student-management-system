<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        /* Header with Navigation */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .navbar .logo {
            color: white;
            font-size: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-right {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .btn-login, .btn-register, .btn-logout {
            padding: 10px 25px;
            border: 2px solid white;
            color: white;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-login:hover, .btn-register:hover {
            background: white;
            color: #667eea;
        }
        
        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .user-info {
            color: white;
            font-size: 14px;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 30px;
            text-align: left;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: flex-start;
            flex-wrap: wrap;
        }
        
        .btn-primary, .btn-secondary {
            padding: 15px 40px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: white;
            color: #667eea;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-secondary:hover {
            background: white;
            color: #667eea;
        }
        
        /* Features Section */
        .features {
            padding: 80px 30px;
            background: white;
        }
        
        .section-title {
            text-align: center;
            font-size: 36px;
            margin-bottom: 60px;
            color: #333;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-card {
            padding: 30px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .feature-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
        }
        
        .feature-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .feature-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .feature-desc {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }
        
        /* About Section */
        .about {
            padding: 80px 30px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .about-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        
        .about-text {
            font-size: 16px;
            line-height: 1.8;
            color: #333;
            margin-bottom: 20px;
        }
        
        /* Modules Section */
        .modules {
            padding: 80px 30px;
            background: white;
        }
        
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .module-item {
            padding: 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .module-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .module-icon {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .module-name {
            font-size: 16px;
            font-weight: 600;
        }
        
        /* Stats Section */
        .stats {
            padding: 60px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .stat-item h3 {
            font-size: 36px;
            margin-bottom: 5px;
        }
        
        .stat-item p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        /* Footer */
        .footer {
            background: #222;
            color: white;
            text-align: center;
            padding: 30px;
            font-size: 14px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 32px;
            }
            
            .hero p {
                font-size: 16px;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-primary, .btn-secondary {
                width: 100%;
                max-width: 300px;
            }
            
            .section-title {
                font-size: 28px;
            }
            
            .navbar {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<!-- Header Navigation -->
<div class="navbar">
    <div class="logo">📚 Student Management System</div>
    <div class="navbar-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="user-info">👤 Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <?php 
            $dash_link = "dashboard.php";
            if ($_SESSION['role'] === 'student') $dash_link = "student_dashboard.php";
            ?>
            <a href="<?php echo $dash_link; ?>" class="btn-login" style="border-color: #2196F3; background: #2196F3;"><span>📊</span> Dashboard</a>
            <a href="auth/logout.php" class="btn-logout"><span>🚪</span> Logout</a>
        <?php else: ?>
            <a href="login_selection.php" class="btn-login"><span>🔐</span> Login</a>
            <a href="registration_selection.php" class="btn-register"><span>📝</span> Register</a>
        <?php endif; ?>
    </div>
</div>

<!-- Messages Section -->
<?php if (isset($_GET['error'])): ?>
    <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px 30px; margin: 10px 0; border-radius: 4px; max-width: 1200px; margin-left: auto; margin-right: auto;">
        <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px 30px; margin: 10px 0; border-radius: 4px; max-width: 1200px; margin-left: auto; margin-right: auto;">
        <strong>✓ Success:</strong> <?php echo htmlspecialchars($_GET['msg']); ?>
    </div>
<?php endif; ?>

<!-- Hero Section -->
<div class="hero">
    <h1>🎓 Student Management System</h1>
    <p>Complete Solution for Educational Institution Management</p>
    <div class="hero-buttons">
        <a href="registration_selection.php" class="btn-primary" style="display: inline-block; text-decoration: none;">📝 Register Now</a>
        <a href="#features" class="btn-secondary" style="display: inline-block; text-decoration: none;">🔍 Explore More</a>
    </div>
</div>

<!-- About Section -->
<div class="about">
    <div class="section-title">About This System</div>
    <div class="about-content">
        <div class="about-text">
            The Student Management System is a comprehensive web-based application designed to streamline educational institution operations. It provides efficient management of students, teachers, courses, attendance, exams, fees, and generates detailed reports.
        </div>
        <div class="about-text">
            Built with modern technologies, our system ensures secure access, easy data management, and real-time tracking of academic activities. Whether you're an administrator managing the institution or a student tracking your progress, this system has you covered.
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="features">
    <div class="section-title">Key Features</div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">👥</div>
            <div class="feature-title">Student Management</div>
            <div class="feature-desc">Complete student profile management with enrollment tracking, photo uploads, and personal details.</div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">👨‍🏫</div>
            <div class="feature-title">Teacher Management</div>
            <div class="feature-desc">Manage teacher information, course assignments, and contact details efficiently.</div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📚</div>
            <div class="feature-title">Course Management</div>
            <div class="feature-desc">Create and manage courses with unique codes, duration, and course details.</div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📋</div>
            <div class="feature-title">Attendance Tracking</div>
            <div class="feature-desc">Mark daily attendance, track presence/absence, and generate attendance reports.</div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📝</div>
            <div class="feature-title">Exam Management</div>
            <div class="feature-desc">Create exams, record marks, view results with auto-grading and student ranking.</div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">💰</div>
            <div class="feature-title">Fee Management</div>
            <div class="feature-desc">Manage fee structures, record payments, and track outstanding balances.</div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">📂</div>
            <div class="feature-title">Assignments</div>
            <div class="feature-desc">Teachers can upload learning resources and assignments while students can submit their work online.</div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">🤖</div>
            <div class="feature-title">Online Quizzes</div>
            <div class="feature-desc">Interactive quiz portal with auto-grading, time limits, and performance tracking.</div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">💬</div>
            <div class="feature-title">Chat Support</div>
            <div class="feature-desc">Integrated messaging system for students to connect with faculties and administration.</div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">🔔</div>
            <div class="feature-title">Notice Board</div>
            <div class="feature-desc">Keep everyone informed with real-time school notifications and announcements.</div>
        </div>
    </div>
</div>

<!-- Modules Section -->
<div class="modules">
    <div class="section-title">Available Modules</div>
    <div class="modules-grid">
        <div class="module-item">
            <div class="module-icon">👥</div>
            <div class="module-name">Students</div>
        </div>
        <div class="module-item">
            <div class="module-icon">👨‍🏫</div>
            <div class="module-name">Teachers</div>
        </div>
        <div class="module-item">
            <div class="module-icon">📚</div>
            <div class="module-name">Courses</div>
        </div>
        <div class="module-item">
            <div class="module-icon">📋</div>
            <div class="module-name">Attendance</div>
        </div>
        <div class="module-item">
            <div class="module-icon">📝</div>
            <div class="module-name">Exams</div>
        </div>
        <div class="module-item">
            <div class="module-icon">💰</div>
            <div class="module-name">Fees</div>
        </div>
        <div class="module-item">
            <div class="module-icon">📊</div>
            <div class="module-name">Reports</div>
        </div>
        <div class="module-item">
            <div class="module-icon">📂</div>
            <div class="module-name">Assignments</div>
        </div>
        <div class="module-item">
            <div class="module-icon">🤖</div>
            <div class="module-name">Quizzes</div>
        </div>
        <div class="module-item">
            <div class="module-icon">💬</div>
            <div class="module-name">Live Chat</div>
        </div>
        <div class="module-item">
            <div class="module-icon">🔔</div>
            <div class="module-name">Notices</div>
        </div>
        <div class="module-item">
            <div class="module-icon">📅</div>
            <div class="module-name">Timetable</div>
        </div>
        <div class="module-item">
            <div class="module-icon">🔐</div>
            <div class="module-name">Secure Access</div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="stats">
    <div class="section-title" style="color: white;">System Capabilities</div>
    <div class="stats-grid">
        <div class="stat-item">
            <h3>9 Tables</h3>
            <p>Complete Database Structure</p>
        </div>
        <div class="stat-item">
            <h3>7 Modules</h3>
            <p>Core Management Functions</p>
        </div>
        <div class="stat-item">
            <h3>4 Reports</h3>
            <p>Comprehensive Analytics</p>
        </div>
        <div class="stat-item">
            <p>Password Hashing & SQL Protection</p>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <p>&copy; 2026 Student Management System. All rights reserved. Built with ❤️ for educational institutions.</p>
</div>

</body>
</html>