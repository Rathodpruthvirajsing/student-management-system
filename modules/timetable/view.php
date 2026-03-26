<?php
include "../../auth/session.php";
include "../../config/db.php";

// Only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

include "../../includes/header.php";
include "../../includes/sidebar.php";

// Fetch all timetables and join with courses
$sql = "SELECT t.*, c.course_name FROM timetables t LEFT JOIN courses c ON t.course_id = c.id ORDER BY FIELD(t.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), t.start_time";
$result = mysqli_query($conn, $sql);
$timetables = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="content">
    <div class="header-section">
        <h2>Timetable Management</h2>
        <a href="add.php" class="btn btn-add">+ Add Schedule</a>
    </div>

    <?php if (isset($_GET['msg'])) { ?>
        <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #c3e6cb;"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php } ?>
    <?php if (isset($_GET['error'])) { ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #f5c6cb;"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php } ?>

    <table class="table" style="width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #e9ecef; text-align: left; color: #333;">
                <th style="padding: 12px 15px;">Course</th>
                <th style="padding: 12px 15px;">Day</th>
                <th style="padding: 12px 15px;">Time</th>
                <th style="padding: 12px 15px;">Subject</th>
                <th style="padding: 12px 15px;">Lab</th>
                <th style="padding: 12px 15px;">Faculties</th>
                <th style="padding: 12px 15px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($timetables) > 0) {
                foreach ($timetables as $t) { ?>
                    <tr style="border-bottom: 1px solid #e9ecef;">
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars($t['course_name']); ?></td>
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars($t['day_of_week']); ?></td>
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars(date('h:i A', strtotime($t['start_time'])) . ' - ' . date('h:i A', strtotime($t['end_time']))); ?></td>
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars($t['subject']); ?></td>
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars($t['room_number']); ?></td>
                        <td style="padding: 12px 15px;"><?php echo htmlspecialchars($t['teacher_name'] ?? 'N/A'); ?></td>
                        <td style="padding: 12px 15px;">
                            <a href="edit.php?id=<?php echo $t['id']; ?>" class="btn btn-edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                            <a href="delete.php?id=<?php echo $t['id']; ?>" class="btn btn-delete" title="Delete" onclick="return confirm('Delete this schedule?');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr><td colspan="7" style="text-align:center; padding: 20px;">No timetable schedules found</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>
