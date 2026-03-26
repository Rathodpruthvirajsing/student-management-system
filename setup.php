<?php
/**
 * Student Management System - Database Setup Script
 * Run this once to initialize the database
 */

$host = "localhost";
$user = "root";
$password = "";
$database = "student_db";

// Create connection
$conn = mysqli_connect($host, $user, $password);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database
$sql_db = "CREATE DATABASE IF NOT EXISTS $database";
if (mysqli_query($conn, $sql_db)) {
    echo "Database created successfully!<br>";
} else {
    die("Error creating database: " . mysqli_error($conn) . "<br>");
}

// Select database
mysqli_select_db($conn, $database);

// Create tables
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','teacher','student','parent') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Also fix existing databases that may have the old ENUM
$sql_fix_role_enum = "ALTER TABLE users MODIFY COLUMN role ENUM('admin','teacher','student','parent') DEFAULT 'admin'";


$sql_courses = "CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_name VARCHAR(100) NOT NULL,
    course_code VARCHAR(50) UNIQUE NOT NULL,
    duration VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$sql_teachers = "CREATE TABLE IF NOT EXISTS teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    phone VARCHAR(15),
    course_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
)";

$sql_students = "CREATE TABLE IF NOT EXISTS students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    enrollment_no VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150),
    phone VARCHAR(15),
    gender ENUM('Male','Female','Other'),
    dob DATE,
    address TEXT,
    photo VARCHAR(255),
    course_id INT,
    admission_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
)";

$sql_attendance = "CREATE TABLE IF NOT EXISTS attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('Present','Absent') NOT NULL,
    marked_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (marked_by) REFERENCES teachers(id) ON DELETE SET NULL
)";

$sql_exams = "CREATE TABLE IF NOT EXISTS exams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    exam_name VARCHAR(100) NOT NULL,
    course_id INT NOT NULL,
    exam_date DATE,
    total_marks INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
)";

$sql_marks = "CREATE TABLE IF NOT EXISTS marks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    exam_id INT NOT NULL,
    marks_obtained INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
)";

$sql_fee_structure = "CREATE TABLE IF NOT EXISTS fee_structure (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    total_fee DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
)";

$sql_fee_payments = "CREATE TABLE IF NOT EXISTS fee_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    payment_date DATE,
    payment_mode ENUM('Cash','UPI','Card','Bank Transfer'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
)";

$sql_parents = "CREATE TABLE IF NOT EXISTS parents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150),
    phone VARCHAR(15),
    student_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL
)";

// Execute table creation
$tables = [
    $sql_users, $sql_courses, $sql_teachers, $sql_students, 
    $sql_attendance, $sql_exams, $sql_marks, $sql_fee_structure, $sql_fee_payments, $sql_parents
];

foreach ($tables as $sql) {
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table: " . mysqli_error($conn) . "<br>");
    }
}

echo "All tables created successfully!<br>";

// Fix existing databases: ensure 'student' is in the role ENUM
if (mysqli_query($conn, $sql_fix_role_enum)) {
    echo "✓ Role ENUM updated to include 'student'!<br>";
} else {
    echo "Note: Could not alter role ENUM: " . mysqli_error($conn) . "<br>";
}


// Insert default admin user
$admin_email = "admin@example.com";
$admin_password = password_hash("admin123", PASSWORD_BCRYPT);

$check_admin = mysqli_query($conn, "SELECT id FROM users WHERE email='$admin_email'");
if (mysqli_num_rows($check_admin) == 0) {
    $sql_admin = "INSERT INTO users (name, email, password, role) VALUES ('Admin User', '$admin_email', '$admin_password', 'admin')";
    if (mysqli_query($conn, $sql_admin)) {
        echo "Default admin user created!<br>";
        echo "<strong>Login Credentials:</strong><br>";
        echo "Email: admin@example.com<br>";
        echo "Password: admin123<br>";
    }
}

mysqli_close($conn);

echo "<br><strong>Setup Complete! Ready to use.</strong><br>";
?>
