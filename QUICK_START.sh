#!/bin/bash
# QUICK START GUIDE - Student Management System

echo "========================================"
echo "Student Management System - Quick Start"
echo "========================================"
echo ""

# Verify Apache is running
echo "Checking Apache..."
netstat -ano | findstr :80 > /dev/null
if [ $? -eq 0 ]; then
    echo "✓ Apache is running on port 80"
else
    echo "✗ Apache is NOT running"
    echo "Please start Apache from XAMPP Control Panel"
fi

echo ""

# Verify MySQL is running
echo "Checking MySQL..."
netstat -ano | findstr :3306 > /dev/null
if [ $? -eq 0 ]; then
    echo "✓ MySQL is running on port 3306"
else
    echo "✗ MySQL is NOT running"
    echo "Please start MySQL from XAMPP Control Panel"
fi

echo ""
echo "========================================"
echo "NEXT STEPS:"
echo "========================================"
echo ""
echo "1. SETUP DATABASE:"
echo "   Open: http://localhost/student-management-system/setup.php"
echo "   Click 'Run Setup' button"
echo ""
echo "2. VERIFY SYSTEM:"
echo "   Open: http://localhost/student-management-system/test_comprehensive.php"
echo "   Check database connection and tables"
echo ""
echo "3. CLEAR BROWSER CACHE:"
echo "   Windows/Linux: Press Ctrl + Shift + R"
echo "   Mac: Press Cmd + Shift + R"
echo ""
echo "4. TEST HOME PAGE:"
echo "   Open: http://localhost/student-management-system/home.php"
echo "   Verify styling and CSS loads correctly"
echo ""
echo "5. TEST LOGIN:"
echo "   Admin: http://localhost/student-management-system/index.php?type=admin"
echo "   Email: admin@example.com"
echo "   Password: password"
echo ""
echo "6. TEST STUDENT LOGIN:"
echo "   Student: http://localhost/student-management-system/index.php?type=student"
echo "   Email: student@example.com"
echo "   Password: password"
echo ""
echo "7. READ FULL REPORT:"
echo "   Open: http://localhost/student-management-system/test_report.html"
echo "   Or: http://localhost/student-management-system/TESTING_COMPLETE.md"
echo ""
echo "========================================"
echo "TESTING CHECKLIST (In Order):"
echo "========================================"
echo ""
echo "□ Apache & MySQL running"
echo "□ Database setup completed"
echo "□ System test passed"
echo "□ Home page loads with CSS"
echo "□ Admin login works"
echo "□ Admin dashboard shows CSS"
echo "□ Student login works"
echo "□ Student dashboard shows CSS"
echo "□ Admin can access all modules"
echo "□ Student cannot access admin modules"
echo "□ Role-based redirects work"
echo "□ Logout works"
echo ""
echo "========================================"
echo "If all checks pass, the system is READY!"
echo "========================================"
