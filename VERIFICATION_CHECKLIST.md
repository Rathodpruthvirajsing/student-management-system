# ✅ VERIFICATION CHECKLIST - Role-Based Access Control

## Quick Verification Tests

Complete these tests to ensure everything is working properly:

---

## 📋 PRE-TEST CHECKLIST

- [ ] XAMPP Apache server is running
- [ ] XAMPP MySQL server is running  
- [ ] Database is accessible
- [ ] User accounts exist in database
- [ ] Browser has JavaScript enabled

---

## 🧪 TEST 1: Home Page Access (Not Logged In)

**URL:** `http://localhost/student-management-system/`

**Expected Results:**
- [ ] Home page loads without errors
- [ ] Navigation bar shows "🔐 Login" button (NOT dashboard button)
- [ ] No user name displayed in header
- [ ] Page content displays correctly (features, modules, stats)
- [ ] No error messages appear

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 2: Login Selection

**Steps:**
1. Click "🔐 Login" button on home page
2. You should land on login_selection.php

**Expected Results:**
- [ ] Page displays two role options:
  - [ ] 👨‍💼 Admin option clickable
  - [ ] 👨‍🎓 Student option clickable
- [ ] No errors on page
- [ ] Smooth styling and display

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 3: Login Form - Admin

**Steps:**
1. Click Admin option on login_selection.php
2. You should see index.php with "Admin Login" header

**Expected Results:**
- [ ] Page shows "Admin Login" label
- [ ] Email field present
- [ ] Password field present  
- [ ] Login button present
- [ ] Links to other pages present (reset password, register, back)

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 4: Admin Login Success

**Steps:**
1. On index.php (Admin), enter admin credentials
2. Email: `admin@example.com` (or your admin email)
3. Password: `admin123` (or your admin password)
4. Click Login button

**Expected Results:**
- [ ] Session is created (no error)
- [ ] Auto-redirects to dashboard.php
- [ ] Admin dashboard loads
- [ ] User name appears in header: "Welcome, [Admin Name]"
- [ ] Admin menu items visible (Manage Students, Courses, etc.)
- [ ] "🚪 Logout" button appears in top-right

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 5: Admin Dashboard Features

**While logged in as Admin:**

- [ ] Dashboard.php loads without errors
- [ ] All dashboard cards display (Students, Courses, Teachers, etc.)
- [ ] Numbers are displayed correctly
- [ ] Quick links are clickable
- [ ] Top navigation has "📊 Admin Dashboard" button (active)
- [ ] "🚪 Logout" button is visible

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 6: Logout Function

**Steps:**
1. Click "🚪 Logout" button

**Expected Results:**
- [ ] Session is destroyed
- [ ] Redirects to home.php
- [ ] User name NO LONGER displayed in header
- [ ] "🔐 Login" button appears (not dashboard button)
- [ ] No errors occur

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 7: Student Login

**Steps:**
1. Click "Login" again on home page
2. Select Student role
3. Enter student credentials (student@example.com / student123)

**Expected Results:**
- [ ] Index.php shows "Student Login" label
- [ ] Login succeeds
- [ ] Auto-redirects to student_dashboard.php
- [ ] Student dashboard displays
- [ ] User name appears: "Welcome, [Student Name]"
- [ ] Student-specific features visible (My Courses, Attendance, etc.)
- [ ] "🚪 Logout" button appears

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 8: Wrong Dashboard Access (Protection)

**Case 1: Student trying to access admin dashboard**

**Steps:**
1. Log in as Student
2. Go directly to: `http://localhost/student-management-system/dashboard.php`

**Expected Results:**
- [ ] Auto-redirects to student_dashboard.php
- [ ] Does NOT show admin dashboard
- [ ] No errors displayed

**Status:** ✅ Pass / ❌ Fail

---

**Case 2: Admin trying to access student dashboard**

**Steps:**
1. Log in as Admin
2. Go directly to: `http://localhost/student-management-system/student_dashboard.php`

**Expected Results:**
- [ ] Auto-redirects to dashboard.php
- [ ] Does NOT show student dashboard
- [ ] No errors displayed

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 9: Direct Login Form Access (Session Check)

**Steps:**
1. Log in as Admin (stay logged in)
2. Go directly to: `http://localhost/student-management-system/index.php`

**Expected Results:**
- [ ] Does NOT show login form
- [ ] Auto-redirects to dashboard.php
- [ ] Session remains active
- [ ] No login form displayed

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 10: Error Messages

**Steps:**
1. Try to log in with wrong password
2. Text: `http://localhost/student-management-system/` in URL bar

**Expected Results:**
- [ ] Redirects back to index.php
- [ ] Error message shows: "Invalid credentials"
- [ ] Can try again immediately
- [ ] Session NOT created (still not logged in)

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 11: Database Check

**Steps:**
1. Create TEST_FLOW.php (if not exists)
2. Go to: `http://localhost/student-management-system/TEST_FLOW.php`

**Expected Results:**
- [ ] Database connection shows as OK
- [ ] Users table exists and shows count
- [ ] All files exist (checkmarks showing)
- [ ] Flow status appropriate for current session

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 12: Message Display on Home Page

**Steps:**
1. Go to: `http://localhost/student-management-system/home.php?error=Test+Error`
2. Check the page

**Expected Results:**
- [ ] Error message displays: "⚠️ Error: Test Error"
- [ ] Red background with white text
- [ ] Properly formatted
- [ ] Message shows at top of page

**Then test success message:**
3. Go to: `http://localhost/student-management-system/home.php?msg=Test+Success`

**Expected Results:**
- [ ] Success message displays: "✓ Success: Test Success"
- [ ] Green background with dark text
- [ ] Properly formatted

**Status:** ✅ Pass / ❌ Fail

---

## 🧪 TEST 13: Multiple Sessions

**Steps:**
1. Open new browser window (Ctrl+N)
2. Go to system in both windows
3. Log in as Admin in Window 1
4. Log in as Student in Window 2
5. Switch between windows

**Expected Results:**
- [ ] Admin window shows admin dashboard
- [ ] Student window shows student dashboard
- [ ] No conflicts between sessions
- [ ] Each maintains proper role display
- [ ] Logout in one doesn't affect other

**Status:** ✅ Pass / ❌ Fail

---

## 📊 OVERALL SUMMARY

| Test # | Description | Status |
|--------|-------------|--------|
| 1 | Home page access | ✅/❌ |
| 2 | Login selection | ✅/❌ |
| 3 | Login form | ✅/❌ |
| 4 | Admin login | ✅/❌ |
| 5 | Admin features | ✅/❌ |
| 6 | Logout function | ✅/❌ |
| 7 | Student login | ✅/❌ |
| 8 | Wrong access protection | ✅/❌ |
| 9 | Direct access check | ✅/❌ |
| 10 | Error messages | ✅/❌ |
| 11 | Database check | ✅/❌ |
| 12 | Message display | ✅/❌ |
| 13 | Multiple sessions | ✅/❌ |

---

## ✅ FINAL STATUS

**Total Tests Passed:** ___ / 13

**System Status:**
- [ ] ✅ All tests passed - PRODUCTION READY
- [ ] ⚠️ Some tests failed - REVIEW NEEDED
- [ ] ❌ Critical tests failed - DEPLOYMENT BLOCKED

---

## 🔧 IF TESTS FAIL

**Common Issues & Solutions:**

| Issue | Possible Cause | Solution |
|-------|----------------|----------|
| Can't access home.php | Server not running | Start XAMPP Apache |
| Database errors | MySQL not running | Start XAMPP MySQL |
| Redirect loops | Session issues | Clear browser cache |
| Wrong user appearing | Session data wrong | Check database users table |
| 404 errors | Wrong URL path | Verify folder structure |
| Blank pages | Database error | Check file_exists in TEST_FLOW.php |

---

## 📝 NOTES FOR THIS TEST RUN

**Date:** _______________  
**Time:** _______________  
**Tester:** _______________

**Additional Notes:**
```
_________________________________________________________________

_________________________________________________________________

_________________________________________________________________
```

---

## ✨ REMEMBER

1. **Always start from home.php** - This is the proper entry point
2. **Test both roles** - Admin and Student flows must work
3. **Test error cases** - Wrong passwords, wrong role access, etc.
4. **Clear cache** - Ctrl+Shift+Del if experiencing issues
5. **Check database** - Ensure user accounts exist with correct roles

---

**Your Student Management System is complete!**

Once all 13 tests pass with ✅, you're ready to deploy to production.

*Test Sheet Last Updated: March 9, 2026*
