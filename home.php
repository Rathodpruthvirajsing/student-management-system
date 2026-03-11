<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexGen SMS | Professional Student Management</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Library (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --secondary: #8b5cf6;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--dark);
            color: var(--text-main);
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--dark);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            z-index: 1000;
            border-bottom: 1px solid var(--glass-border);
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
            font-size: 15px;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn-portal {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
            transition: 0.3s;
        }

        .btn-portal:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.7)), url('assets/images/hero-bg.png');
            background-size: cover;
            background-position: center;
            padding: 0 20px;
            position: relative;
        }

        .hero::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 200px;
            background: linear-gradient(to top, var(--dark), transparent);
        }

        .hero-content h1 {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }

        .hero-content h1 span {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            font-size: 1.2rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto 40px;
        }

        /* Floating Cards Animation */
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .float {
            position: absolute;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(5px);
            padding: 15px;
            border-radius: 12px;
            animation: floatAnim 6s infinite ease-in-out;
        }

        @keyframes floatAnim {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Features */
        .section-padding {
            padding: 100px 50px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .section-title p {
            color: var(--text-muted);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-box {
            background: var(--dark-light);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            transition: 0.4s;
            position: relative;
            overflow: hidden;
        }

        .feature-box:hover {
            transform: translateY(-10px);
            border-color: var(--primary);
            background: rgba(99, 102, 241, 0.05);
        }

        .feature-box i {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 25px;
        }

        .feature-box h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .feature-box p {
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* Counter Section */
        .counter-section {
            background: var(--dark-light);
            display: flex;
            justify-content: space-around;
            padding: 80px 20px;
            border-radius: 30px;
            margin: 0 50px;
            border: 1px solid var(--glass-border);
        }

        .counter-box h3 {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 5px;
        }

        /* Footer */
        footer {
            padding: 50px;
            text-align: center;
            border-top: 1px solid var(--glass-border);
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 { font-size: 2.5rem; }
            nav { padding: 15px 20px; }
            .nav-links { display: none; }
            .section-padding { padding: 60px 20px; }
            .counter-section { flex-direction: column; gap: 40px; margin: 0 20px; }
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo">
            <i class="fas fa-graduation-cap"></i> NEXGEN SMS
        </div>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#about">About</a>
            <a href="#stats">Stats</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo $_SESSION['role'] === 'admin' ? 'dashboard.php' : 'student_dashboard.php'; ?>" class="btn-portal">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
            <?php else: ?>
                <a href="login_selection.php" class="btn-portal">
                    <i class="fas fa-lock"></i> Student Portal
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero">
        <div class="hero-content" data-aos="zoom-in">
            <h1>Seamless <span>Education</span> Management</h1>
            <p>Empowering institutions with next-generation tools for student success, teacher efficiency, and administrative excellence.</p>
            <div class="hero-btns">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login_selection.php" class="btn-portal" style="padding: 15px 40px; font-size: 18px;">Get Started</a>
                <?php else: ?>
                    <a href="<?php echo $_SESSION['role'] === 'admin' ? 'dashboard.php' : 'student_dashboard.php'; ?>" class="btn-portal" style="padding: 15px 40px; font-size: 18px;">Return to Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <section id="features" class="section-padding">
        <div class="section-title" data-aos="fade-up">
            <h2>Core Modules</h2>
            <p>Integrated solutions for every aspect of campus management</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-box" data-aos="fade-up" data-aos-delay="100">
                <i class="fas fa-user-graduate"></i>
                <h3>Student Matrix</h3>
                <p>Complete 360-degree profiles, enrollment histories, and digital documentation at your fingertips.</p>
            </div>
            
            <div class="feature-box" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Faculty Hub</h3>
                <p>Assign courses, track performance, and bridge the communication gap between teachers and students.</p>
            </div>
            
            <div class="feature-box" data-aos="fade-up" data-aos-delay="300">
                <i class="fas fa-book-open"></i>
                <h3>Course Engine</h3>
                <p>Advanced curriculum management with flexible durations and dynamic coding assignments.</p>
            </div>

            <div class="feature-box" data-aos="fade-up" data-aos-delay="100">
                <i class="fas fa-calendar-check"></i>
                <h3>Real-time Attendance</h3>
                <p>Dynamic marking system with instant reporting and absence notification protocols.</p>
            </div>
            
            <div class="feature-box" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-file-invoice-dollar"></i>
                <h3>Financial Portal</h3>
                <p>Comprehensive fee tracking, automated invoices, and secure payment status monitoring.</p>
            </div>
            
            <div class="feature-box" data-aos="fade-up" data-aos-delay="300">
                <i class="fas fa-chart-pie"></i>
                <h3>Intelligent Analytics</h3>
                <p>Generate visual reports on students, exams, and institutional trends with one click.</p>
            </div>
        </div>
    </section>

    <div id="stats" class="counter-section" data-aos="fade-right">
        <div class="counter-box">
            <h3><span class="count">25</span>+</h3>
            <p>Active Students</p>
        </div>
        <div class="counter-box">
            <h3><span class="count">10</span>+</h3>
            <p>Core Modules</p>
        </div>
        <div class="counter-box">
            <h3><span class="count">100</span>%</h3>
            <p>Secure Database</p>
        </div>
        <div class="counter-box">
            <h3><span class="count">24</span>/7</h3>
            <p>Support Ready</p>
        </div>
    </div>

    <section id="about" class="section-padding" style="background: var(--dark-light); margin-top: 100px;">
        <div class="section-title" data-aos="fade-up">
            <h2>Why NexGen SMS?</h2>
        </div>
        <div style="max-width: 800px; margin: 0 auto; text-align: center; color: var(--text-muted); line-height: 2;" data-aos="fade-up">
            Our platform is built on the principle of efficiency. We eliminate manual paperwork and replace it with a high-performance, responsive environment that adapts to your institution's specific needs. With role-based security and a focus on UX, NexGen is the ultimate Choice for modern educators.
        </div>
    </section>

    <footer>
        <p>&copy; 2026 NexGen Student Management System. All Rights Reserved.</p>
        <div style="margin-top: 15px; font-size: 1.2rem; display: flex; justify-content: center; gap: 20px;">
            <i class="fab fa-facebook hover-primary"></i>
            <i class="fab fa-twitter"></i>
            <i class="fab fa-instagram"></i>
            <i class="fab fa-linkedin"></i>
        </div>
    </footer>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        // Sticky Nav Effect
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.style.padding = '10px 50px';
                nav.style.background = 'rgba(15, 23, 42, 0.95)';
            } else {
                nav.style.padding = '20px 50px';
                nav.style.background = 'rgba(15, 23, 42, 0.8)';
            }
        });
    </script>
</body>
</html>