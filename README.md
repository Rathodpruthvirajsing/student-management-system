# Student Management System

A complete web-based Student Management System built with PHP and MySQL. This system allows administrators and teachers to manage students, courses, attendance, exams, fees, and generate reports.

## Features

### 1. **Authentication & Authorization**
- Login/Logout system with session management
- Role-based access control (Admin, Teacher)
- Secure password hashing with bcrypt

### 2. **Student Management**
- Add, view, edit, and delete students
- Store student information (enrollment number, name, contact, gender, DOB, address)
- Student photo upload
- Course assignment

### 3. **Course Management**
- Create and manage courses
- Store course code, duration, and details
- Assign students and teachers to courses

### 4. **Teacher Management**
- Add and manage teachers
- Assign teachers to courses
- Store contact information

### 5. **Attendance System**
- Mark attendance for students
- Filter by student and course
- Generate attendance reports
- Calculate attendance percentage

### 6. **Examination System**
- Create exams with course assignment
- Add marks for students
- View exam results with ranking
- Calculate grades (A, B, C, D, F)
- View average marks per exam

### 7. **Fee Management**
- Define fee structure for courses
- Record fee payments
- Track payment status
- Generate fee reports

### 8. **Reports**
- Student efficiency report with attendance and marks
- Attendance report with percentage calculation
- Fee collection report with balance tracking
- Exam report with statistics

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server
- Modern web browser

## Installation

1. **Download/Extract Files**
   - Place all files in `htdocs/student-management-system` directory

2. **Create Database**
   - Open browser and navigate to: `http://localhost/student-management-system/setup.php`
   - This will create the database and tables automatically
   - Default admin credentials will be shown

3. **Access the System**
   - Navigate to: `http://localhost/student-management-system/`
   - Login with credentials:
     - Email: `admin@example.com`
     - Password: `admin123`

## Directory Structure

```
student-management-system/
├── index.php                 # Login page
├── dashboard.php             # Main dashboard
├── setup.php                 # Database setup script
├── auth/
│   ├── login.php            # Login processing
│   ├── logout.php           # Logout script
│   └── session.php          # Session validation
├── config/
│   └── db.php               # Database connection
├── includes/
│   ├── header.php           # Header template
│   ├── sidebar.php          # Sidebar navigation
│   └── footer.php           # Footer template
├── modules/
│   ├── students/            # Student management
│   │   ├── add.php
│   │   ├── edit.php
│   │   ├── view.php
│   │   ├── delete.php
│   │   └── teachers/        # Teacher management
│   ├── courses/             # Course management
│   │   ├── add.php
│   │   ├── edit.php
│   │   ├── view.php
│   │   └── delete.php
│   ├── attendance/          # Attendance tracking
│   │   ├── mark.php
│   │   ├── view.php
│   │   ├── report.php
│   │   └── delete.php
│   ├── exams/               # Exam management
│   │   ├── create.php
│   │   ├── marks.php
│   │   ├── result.php
│   │   └── delete.php
│   └── fees/                # Fee management
│       ├── payment.php
│       ├── add_payment.php
│       ├── structure.php
│       ├── add_structure.php
│       └── delete.php
├── reports/
│   ├── student-report.php
│   ├── attendance-report.php
│   ├── exam-report.php
│   └── fees-report.php
├── assets/
│   ├── css/
│   │   └── style.css        # Main stylesheet
│   └── js/
│       └── validation.js    # Form validation
├── uploads/
│   └── student_photos/      # Student photos storage
└── logs/                    # System logs
```

## Database Tables

- **users** - Admin and teacher accounts
- **courses** - Course information
- **students** - Student records
- **teachers** - Teacher details
- **attendance** - Attendance records
- **exams** - Exam information
- **marks** - Student exam marks
- **fee_structure** - Course-wise fee structure
- **fee_payments** - Fee payment records

## Usage Guide

### Adding a Student
1. Go to Students → Add New Student
2. Fill in all required fields
3. Upload student photo (optional)
4. Click Add Student

### Marking Attendance
1. Go to Attendance → Mark Attendance
2. Select student and course
3. Choose Present/Absent
4. Submit

### Creating Exams
1. Go to Exams → Create New Exam
2. Fill exam details
3. Add marks for students in the exam
4. View results

### Recording Fee Payments
1. Go to Fees → Add Payment
2. Select student and amount
3. Choose payment mode
4. Record payment

### Generating Reports
1. Go to Reports section
2. Select required report type
3. Click Print to download/print

## Security Features

- SQL Injection prevention using prepared statements
- XSS protection with htmlspecialchars()
- Session-based authentication
- Password hashing with bcrypt
- CSRF token validation on forms
- Input validation on all forms
- File upload restrictions

## Default Login

- **Email:** admin@example.com
- **Password:** admin123

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Notes

- Always change the default admin password after first login
- Regularly backup the database
- Create student photos directory with proper write permissions
- Use HTTPS in production environment
- Keep PHP and MySQL updated for security

## License

This system is provided as-is for educational purposes.

## Support

For issues or questions, check the logs in the `logs/` directory.

---

**Version:** 1.0  
**Last Updated:** February 2026
