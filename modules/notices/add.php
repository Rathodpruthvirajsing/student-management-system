<?php
include "../../auth/session.php";
include "../../config/db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../home.php?error=Unauthorized");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $expire_date = trim($_POST['expire_date']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $posted_by = $_SESSION['user_id'];

    if (empty($expire_date)) {
        $expire_date = "NULL";
    } else {
        $expire_date = "'$expire_date'";
    }

    if (empty($title) || empty($content)) {
        $error = "Title and Content are required";
    } else {
        $sql = "INSERT INTO notices (title, content, expire_date, is_active, posted_by) 
                VALUES ('$title', '$content', $expire_date, $is_active, '$posted_by')";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: view.php?msg=Notice posted successfully");
            exit();
        } else {
            $error = "Error posting notice: " . mysqli_error($conn);
        }
    }
}

// Now include header and sidebar after processing logic
include "../../includes/header.php";
include "../../includes/sidebar.php";
?>

<div class="content">
    <h2>Post New Notice</h2>
    
    <?php if ($error) echo '<div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #f5c6cb;">' . $error . '</div>'; ?>

    <form method="POST" class="form-container" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 800px;">
        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Notice Title *</label>
            <input type="text" name="title" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Content *</label>
            <textarea name="content" required rows="6" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; resize: vertical;"></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 15px; width: 50%;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Expiry Date</label>
            <input type="date" name="expire_date" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            <small style="color: #6c757d;">Leave blank if it never expires</small>
        </div>

        <div class="form-group" style="margin-bottom: 25px;">
            <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500;">
                <input type="checkbox" name="is_active" checked style="margin-right: 10px; width: 18px; height: 18px;">
                Is Active (Show to students immediately)
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">Post Notice</button>
            <a href="view.php" style="background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-size: 16px; margin-left: 10px;">Cancel</a>
        </div>
    </form>
</div>

<?php include "../../includes/footer.php"; ?>
