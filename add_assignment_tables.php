<?php
include "config/db.php";

$sql1 = "CREATE TABLE IF NOT EXISTS assignments (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    course_id INT(11) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    due_date DATE,
    created_by INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
)";

$sql2 = "CREATE TABLE IF NOT EXISTS assignment_submissions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT(11) NOT NULL,
    student_id INT(11) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    teacher_remarks TEXT,
    grade VARCHAR(10),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql1) && mysqli_query($conn, $sql2)) {
    echo "Assignment tables created successfully.";
} else {
    echo "Error creating tables: " . mysqli_error($conn);
}
?>
