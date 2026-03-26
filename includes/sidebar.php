<div class="sidebar">
    <ul>
        <?php
        // Calculate the correct path prefix based on script depth dynamically using filesystem
        $project_root = str_replace('\\', '/', dirname(__DIR__));
        $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
        $relative_path = trim(str_ireplace($project_root, '', $script_dir), '/');
        $depth = $relative_path ? substr_count($relative_path, '/') + 1 : 0;
        $path_prefix = str_repeat('../', $depth);
        ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <!-- Admin Menu -->
            <li><a href="<?php echo $path_prefix; ?>dashboard.php">📊 Dashboard</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/students/view.php">👥 Students</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/students/teachers/view.php">👨‍🏫 Teachers</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/subjects/view.php">📖 Subjects</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/teachers/assign.php">🔗 Assign Teachers</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/courses/view.php">📚 Courses</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/attendance/view.php">📋 Attendance</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/exams/create.php">📝 Exams</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/assignments/view.php">📂 Assignments</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/timetable/view.php">📅 Timetable</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/notices/view.php">🔔 Notices</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/chat/admin.php">💬 Chat Support</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/leaves/admin.php">📜 Manage Leaves</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/quizzes/teacher_view.php">🤖 Manage Quizzes</a></li>
            <li>
                <a href="#" onclick="toggleMenu(this); return false;" style="display: flex; justify-content: space-between;">💰 Fees <span>▼</span></a>
                <ul class="submenu" style="display: none;">
                    <li><a href="<?php echo $path_prefix; ?>modules/fees/payment.php">Payments</a></li>
                    <li><a href="<?php echo $path_prefix; ?>modules/fees/structure.php">Fee Structure</a></li>
                </ul>
            </li>
            <li>
                <a href="#" onclick="toggleMenu(this); return false;" style="display: flex; justify-content: space-between;">📊 Reports <span>▼</span></a>
                <ul class="submenu" style="display: none;">
                    <li><a href="<?php echo $path_prefix; ?>reports/student-report.php">Student Report</a></li>
                    <li><a href="<?php echo $path_prefix; ?>reports/attendance-report.php">Attendance Report</a></li>
                    <li><a href="<?php echo $path_prefix; ?>reports/exam-report.php">Exam Report</a></li>
                    <li><a href="<?php echo $path_prefix; ?>reports/fees-report.php">Fees Report</a></li>
                </ul>
            </li>
        <?php elseif ($_SESSION['role'] === 'teacher'): ?>
            <!-- Teacher Menu -->
            <li><a href="<?php echo $path_prefix; ?>modules/teachers/dashboard.php">📊 Dashboard</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/attendance/mark.php">📋 Mark Attendance</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/teachers/students_coursewise.php">👥 Student List</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/teachers/student_performance.php">📈 Performance</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/assignments/view.php">📂 Assignments</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/timetable/teacher_view.php">📅 My Timetable</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/notices/view.php">🔔 School Notices</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/chat/admin.php">💬 Chat with Students</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/quizzes/teacher_view.php">🤖 Online Quizzes</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/leaves/my_leaves.php">📅 My Leaves</a></li>
        <?php elseif ($_SESSION['role'] === 'parent'): ?>
            <!-- Parent Menu -->
            <li><a href="<?php echo $path_prefix; ?>modules/parents/dashboard.php">📊 Dashboard</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/attendance/student_view.php">📋 Child Attendance</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/exams/student_results.php">📝 Child Results</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/assignments/student_view.php">📂 Child Assignments</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/timetable/student_view.php">📅 Timetable</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/notices/view.php">🔔 Notices</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/chat/student.php">💬 Connect with Faculty</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/fees/student_fees.php">💰 Fee Status</a></li>
        <?php else: ?>
            <!-- Student Menu -->
            <li><a href="<?php echo $path_prefix; ?>student_dashboard.php">📊 Dashboard</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/attendance/student_view.php">📋 My Attendance</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/exams/student_results.php">📝 My Results</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/assignments/student_view.php">📂 Assignments</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/timetable/student_view.php">📅 Timetable</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/notices/view.php">🔔 School Notices</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/chat/student.php">💬 Chat with Faculty</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/quizzes/student_view.php">🤖 Online Quizzes</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/leaves/my_leaves.php">📅 Apply Leave</a></li>
            <li><a href="<?php echo $path_prefix; ?>student_info.php">📚 Course Info</a></li>
            <li><a href="<?php echo $path_prefix; ?>modules/fees/student_fees.php">💰 Fee Status</a></li>
        <?php endif; ?>
        <li><a href="<?php echo $path_prefix; ?>auth/logout.php" class="logout-link">🚪 Logout</a></li>
    </ul>
</div>

<script>
function toggleMenu(elem) {
    var submenu = elem.nextElementSibling;
    if (submenu && submenu.classList && submenu.classList.contains('submenu')) {
        submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
    } else if (submenu && submenu.tagName === 'UL') {
        submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
    }
}
</script>