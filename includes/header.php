<?php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="stylesheet" href="<?php 
        // Calculate the correct path to assets based on script depth dynamically using filesystem
        $project_root = str_replace('\\', '/', dirname(__DIR__));
        $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
        $relative_path = trim(str_ireplace($project_root, '', $script_dir), '/');
        $depth = $relative_path ? substr_count($relative_path, '/') + 1 : 0;
        $asset_prefix = str_repeat('../', $depth);
        echo $asset_prefix;
    ?>assets/css/style.css">
</head>
<body>

<div class="header">
    <div class="header-content">
        <h1>Student Management System</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
        </div>
    </div>
</div>

<div class="container">
