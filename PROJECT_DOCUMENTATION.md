# Student Management System - Project Documentation

**University:** Parul University  
**Department:** FITCS, Department of MCA  
**Program:** MCA Semester-4 / IMCA-8  

---

## 1. Project Profile

### 1.1 Project Definition
The **Student Management System (SMS)** is a web-based application designed to automate the administrative tasks of an educational institution. It handles student registrations, course management, attendance tracking, and fee status monitoring through a centralized platform.

### 1.2 Project Description
The system provides separate portals for Administrators and Students. Admins can manage the core data (students, teachers, courses) and track institutional performance. Students can view their personal profiles, attendance records, exam results, and fee payment history. The application is built using a modern PHP/MySQL stack with a focus on responsive UI/UX.

### 1.3 Need for New System
*   **Manual Records:** Traditional paper-based systems are prone to errors and loss.
*   **Data Fragmentation:** Difficulty in synchronizing attendance records with student profiles.
*   **Communication Gap:** Students often lack real-time access to their performance and fee status.
*   **Scalability:** The new system allows the institution to handle a growing number of students without increasing administrative overhead.

### 1.4 Proposed System & Features
*   **Role-Based Access:** Secure login for Students and Administrators.
*   **Student Management:** Comprehensive profiles including enrollment, personal details, and courses.
*   **Attendance Tracking:** Automated attendance logging with per-course visualization.
*   **Fee Management:** Tracking payments and viewing current balance/status.
*   **Performance Monitoring:** Viewing exam results and academic progress.
*   **Responsive UI:** Mobile-first design for access on any device.

### 1.5 Tools & Technology Used
*   **Frontend:** HTML5, Vanilla CSS3 (Custom styling), JavaScript.
*   **Backend:** PHP 8.x.
*   **Database:** MySQL / MariaDB.
*   **Server:** Apache (XAMPP).
*   **Version Control:** Git.

---

## 2. Requirement Analysis

### 2.1 Feasibility Study
*   **Technical Feasibility:** The project uses PHP/MySQL, which is widely supported and scalable for local or hosted environments.
*   **Operational Feasibility:** The intuitive UI requires minimal training for both staff and students.
*   **Economic Feasibility:** Built using Open Source tools, keeping the implementation cost low.

### 2.2 Users of the System
1.  **Administrator:** Full control over students, teachers, courses, attendance, and reports.
2.  **Student:** Access to personal dashboard, academic details, and financial status.

### 2.3 System Modules
*   **Auth Module:** Handles secure login, session management, and password resets.
*   **Student Module:** CRUD operations for student records.
*   **Course Module:** Management of available courses and durations.
*   **Attendance Module:** Tracking daily presence for students in specific courses.
*   **Fees Module:** Logging payments and calculating pending status.
*   **Reports Module:** Generating summaries for attendance, exams, and fees.

### 2.4 Hardware & Software Requirements
*   **Hardware:**
    *   Minimum 4GB RAM.
    *   Dual-core processor or higher.
    *   100MB Disk Space for application files.
*   **Software:**
    *   Windows/Linux/OSX.
    *   XAMPP / WAMP / LAMP Stack (PHP 8+, MySQL 5.7+).
    *   Web Browser (Chrome, Firefox, Safari).

---

## 3. Design

### 3.1 Data Dictionary

| Table | Column | Type | Description |
| :--- | :--- | :--- | :--- |
| **users** | id | INT (PK) | Unique ID for login |
| | name | VARCHAR | User's full name |
| | email | VARCHAR (UNI)| Login email |
| | password | VARCHAR | BCRYPT Hashed password |
| | role | ENUM | admin, teacher, student |
| **students**| id | INT (PK) | Unique student ID |
| | enrollment_no| VARCHAR | Institutional Enrollment No |
| | course_id | INT (FK) | Reference to courses table |
| | email | VARCHAR | Matching email from users table |
| **courses** | id | INT (PK) | Unique course ID |
| | course_name | VARCHAR | Name of the program (e.g., BCA) |
| | course_code | VARCHAR | Short code (e.g., BCA-01) |
| **attendance**| id | INT (PK) | Log ID |
| | student_id | INT (FK) | Reference to student |
| | status | ENUM | Present, Absent |
| | attendance_date | DATE | Date of attendance |

### 3.2 ER Diagram Logic
*   **User - Student:** One-to-One relationship (Every student has one login account).
*   **Course - Student:** One-to-Many (One course can have multiple students enrolled).
*   **Student - Attendance:** One-to-Many (One student has many attendance records).
*   **Student - Fees:** One-to-Many (Multiple payments ledger per student).
*   **Teacher - Course:** One-to-One (Currently modeled as one primary teacher per course).

---

## 4. Implementation

### 4.1 Coding Conventions
*   **Variable Naming:** CamelCase for frontend logic, snake_case for PHP/SQL/Back-end.
*   **Security:** Multi-layer security including hashed passwords and session-based role verification.
*   **Modular Design:** Use of `include` for header, sidebar, and footer to maintain DRY (Don't Repeat Yourself) principles.

### 4.2 Code Snippet: Robust Path Resolution Logic
To ensure the sidebar and CSS work correctly regardless of folder depth (Localhost root vs subdirectory), we implemented this dynamic resolver:

```php
// Dynamic path prefixing for nested modules logic
$project_root = str_replace('\\', '/', dirname(__DIR__));
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
$relative_path = trim(str_ireplace($project_root, '', $script_dir), '/');
$depth = $relative_path ? substr_count($relative_path, '/') + 1 : 0;
$path_prefix = str_repeat('../', $depth);
```

---

## 5. Testing

### 5.1 Test Strategy
We utilized **Unit Testing** for database connections and **End-to-End (E2E) Testing** for the user interface and navigation.

### 5.2 Test Cases

| Test Case ID | Description | Input | Expected Result | Status |
| :--- | :--- | :--- | :--- | :--- |
| TC-01 | Admin Login | admin@example.com | Redirection to admin dashboard | PASS |
| TC-02 | CSS Pathing | Nested Page access | Style.css loads correctly | PASS |
| TC-03 | Attendance Log| Marking "Present" | Record saved in DB correctly | PASS |
| TC-04 | User Registration| New Student Form | User created in 'users' and 'students' | PASS |
| TC-05 | RBAC Security | Direct URL access | Unauthorized access restricted | PASS |

---

**Generated by Antigravity AI for Final Semester Project Milestone.**
