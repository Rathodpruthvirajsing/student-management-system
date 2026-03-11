# Quick Start Guide - Student Management System

## 🚀 Get Started in 3 Steps

### Step 1: Database Setup
1. Start XAMPP (Apache & MySQL)
2. Open your browser and go to: **http://localhost/student-management-system/setup.php**
3. You should see:
   - ✅ Database created successfully
   - ✅ All tables created successfully  
   - ✅ Default admin user created
   - 📧 Email: `admin@example.com`
   - 🔑 Password: `admin123`

### Step 2: Access the System
- Go to: **http://localhost/student-management-system/**
- Login with the credentials above

### Step 3: Start Using
- **Admin Dashboard**: Manage students, courses, attendance, exams, fees
- **Reports**: View all analytics and reports

---

## 📋 Default Logins

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | admin123 |

---

## 🔧 Troubleshooting

### Issue: "Cannot connect to database"
**Fix**: 
- Ensure MySQL is running in XAMPP
- Edit `config/db.php` if your database credentials differ

### Issue: "Session errors or redirects to login"
**Fix**:
- Check that `logs/` folder exists and is writable
- Verify database tables were created in Steps 1

### Issue: "Missing files or 404 errors"
**Fix**:
- Ensure all files are in `c:\xampp\htdocs\student-management-system\`
- Check file permissions

### Issue: "Upload/Photo storage not working"
**Fix**:
- Ensure `uploads/student_photos/` folder exists and is writable
- Fix: Run `mkdir uploads\student_photos` if needed

---

## 📁 Project Structure

```
student-management-system/
├── setup.php              ⭐ Run this first!
├── index.php              → Login page
├── dashboard.php          → Admin dashboard
├── student_dashboard.php  → Student dashboard
├── config/                → Database config
├── auth/                  → Login/logout/session
├── modules/               → All features (students, courses, etc)
├── reports/               → Analytics
├── assets/                → CSS & JS
└── uploads/               → Student photos
```

---

## ✨ Key Features

- ✅ Authentication (Admin, Teacher, Student)
- ✅ Student Management
- ✅ Course Management
- ✅ Attendance Tracking
- ✅ Exam Management with Grades
- ✅ Fee Management
- ✅ Reports & Analytics
- ✅ Secure Session Management
- ✅ SQL Injection Prevention
- ✅ XSS Protection

---

## 🆘 Need Help?

1. Check the diagnostic tool: **http://localhost/student-management-system/diagnose_login.php**
2. Review logs in `logs/` folder
3. Check `README.md` for detailed documentation
4. Review `PROJECT_AUDIT_REPORT.md` for feature completeness

---

**Status**: ✅ Project is fully functional and ready to use!  
**Last Updated**: February 25, 2026
