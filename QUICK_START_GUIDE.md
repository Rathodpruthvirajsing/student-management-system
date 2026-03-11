# QUICK START GUIDE - Student Management System

## 🚀 Getting Started

### Step 1: Access the System
Open your browser and navigate to:
```
http://localhost/student-management-system/
```

You will land on the **home.php** page - the main landing page.

---

## 👥 LOGIN PROCESS

### For New Users (Not Logged In)

1. Click the **🔐 Login** button in the top-right navigation
2. You will be taken to **Role Selection Page**
3. Select your role:
   - **👨‍💼 Admin** - For administrators/staff
   - **👨‍🎓 Student** - For students
4. Enter your credentials:
   - **Email Address**: Your registered email
   - **Password**: Your password
5. Click **Login**
6. System automatically redirects you to your dashboard

---

## 📊 DASHBOARDS AFTER LOGIN

### Admin Dashboard Features:
- 📊 View statistics (students, courses, teachers, exams)
- 👥 Manage Students
- 📚 Manage Courses
- 👨‍🏫 Manage Teachers
- 📋 Mark Attendance
- 📝 Create & Manage Exams
- 💰 Manage Fees
- 📈 Generate Reports

**Access URL:** `http://localhost/student-management-system/dashboard.php`

### Student Dashboard Features:
- 📊 View your academic progress
- 📚 View enrolled courses
- 📋 Check attendance records
- 📝 View exam results and marks
- 💰 View fee payment status
- 📄 Download documents

**Access URL:** `http://localhost/student-management-system/student_dashboard.php`

---

## 🚪 LOGOUT

1. Click the **🚪 Logout** button in the top-right corner
2. Your session will be terminated
3. You will be redirected to the home page
4. To login again, repeat the login process

---

## ⚠️ IMPORTANT REMINDERS

### ✅ DO's:
- ✅ Always start from the home page
- ✅ Use "Back" or "Home" buttons to navigate
- ✅ Logout when done with your session
- ✅ Clear browser cache if experiencing issues

### ❌ DON'Ts:
- ❌ Don't bookmark dashboard URLs directly
- ❌ Don't share your login credentials
- ❌ Don't access the system from public computers without logging out
- ❌ Don't refresh the page multiple times rapidly

---

## 🔒 SECURITY NOTES

1. **Session Timeout**: Your session will automatically expire after 30 minutes of inactivity
2. **Password Protection**: Your password is securely stored (bcrypt hashed)
3. **Role-Based Access**: You can only access features for your role
4. **SQL Protection**: All inputs are protected against SQL injection

---

## 🆘 TROUBLESHOOTING

### Problem: "Invalid Credentials" Error
**Solution:** Check your email and password are correct. Contact administrator if account doesn't exist.

### Problem: "Unauthorized Access" Message
**Solution:** This means you're trying to access a page you don't have permission for. 
- Admins should use Admin Dashboard
- Students should use Student Dashboard

### Problem: Session Expired Message
**Solution:** Your session has expired due to inactivity. Simply login again.

### Problem: Login Button Doesn't Work
**Solution:** 
1. Check if you're already logged in (check top-right corner)
2. Clear browser cache: `Ctrl + Shift + Del`
3. Try a different browser

### Problem: "Student Record Not Found"
**Solution:** Contact administrator to ensure your student record is created in the database.

---

## 👨‍💼 FOR ADMINISTRATORS

### Initial Setup:
1. Login with your admin account
2. Go to "Manage Students" to add student records
3. Go to "Manage Courses" to create courses
4. Go to "Manage Teachers" to add teacher information

### Creating Student Accounts:
1. Need to create a user account first (contact system admin)
2. Then create a student record linked to that user
3. Student can then login with their credentials

---

## 👨‍🎓 FOR STUDENTS

### First Time Login:
1. You should have received credentials from your institution
2. Go to login page and select "Student"
3. Enter your email and password
4. You'll see your personalized student dashboard

### Viewing Your Progress:
1. On your dashboard, you can see:
   - Your enrolled courses
   - Attendance percentage
   - Exam results and marks
   - Fee payment status
   - Any announcements from institution

---

## 📞 NEED HELP?

If you encounter any issues:
1. Check the troubleshooting section above
2. Check if you're using the correct role (Admin/Student)
3. Verify your browser allows cookies and JavaScript
4. Contact your system administrator

---

## ✨ SYSTEM FEATURES AT A GLANCE

| Feature | Admin | Student |
|---------|-------|---------|
| View Dashboard | ✅ | ✅ |
| Manage Students | ✅ | ❌ |
| Manage Courses | ✅ | ✅ (View Only) |
| Mark Attendance | ✅ | ❌ |
| View Attendance | ✅ | ✅ |
| Manage Exams | ✅ | ❌ |
| View Exam Results | ✅ | ✅ |
| Record Fees | ✅ | ❌ |
| View Fee Status | ✅ | ✅ |
| Generate Reports | ✅ | ✅ (Limited) |

---

**Welcome to Student Management System!** 🎓

For more technical details, see the README.md or ROLE_BASED_ACCESS_FIX.md files.

*Last Updated: March 9, 2026*
