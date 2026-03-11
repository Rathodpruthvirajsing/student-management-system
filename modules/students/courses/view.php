<?php
include "../../auth/session.php";
include "../../config/db.php";
include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <h2>Course List</h2>

    <a href="add.php" class="btn btn-add">➕ Add New Course</a><br><br>

    <table width="100%">
        <tr>
            <th>ID</th>
            <th>Course Name</th>
            <th>Course Code</th>
            <th>Duration</th>
        </tr>

        <?php
        $query = "SELECT * FROM courses ORDER BY id DESC";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['course_name']; ?></td>
                <td><?php echo $row['course_code']; ?></td>
                <td><?php echo $row['duration']; ?></td>
            </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='4' align='center'>No Courses Found</td></tr>";
        }
        ?>
    </table>
</div>

<?php include "../../includes/footer.php"; ?>