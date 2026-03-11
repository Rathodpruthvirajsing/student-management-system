<?php
include "config/db.php";

// Check if confirm parameter is set
if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Clear Database - WARNING</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                margin: 50px auto;
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .warning {
                background: #fff3cd;
                border: 2px solid #ffc107;
                color: #856404;
                padding: 20px;
                border-radius: 4px;
                margin-bottom: 20px;
            }
            .warning h2 {
                margin-top: 0;
                color: #ff6b6b;
            }
            .warning ul {
                margin: 10px 0;
                padding-left: 20px;
            }
            .warning li {
                margin: 8px 0;
            }
            .buttons {
                display: flex;
                gap: 10px;
                margin-top: 30px;
            }
            .btn {
                flex: 1;
                padding: 12px 20px;
                border: none;
                border-radius: 4px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
            }
            .btn-danger {
                background: #dc3545;
                color: white;
            }
            .btn-danger:hover {
                background: #c82333;
            }
            .btn-cancel {
                background: #6c757d;
                color: white;
            }
            .btn-cancel:hover {
                background: #5a6268;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="warning">
                <h2>⚠️ WARNING: DESTRUCTIVE OPERATION</h2>
                <p><strong>You are about to delete ALL data from the database!</strong></p>
                <p>This action will:</p>
                <ul>
                    <li>❌ Delete ALL users (Admin and Student accounts)</li>
                    <li>❌ Delete ALL student records</li>
                    <li>❌ Delete ALL courses</li>
                    <li>❌ Delete ALL attendance records</li>
                    <li>❌ Delete ALL exams and marks</li>
                    <li>❌ Delete ALL fee payments</li>
                    <li>❌ Delete ALL other data in the database</li>
                </ul>
                <p><strong style="color: #ff6b6b;">This action CANNOT be undone!</strong></p>
            </div>

            <div class="buttons">
                <form method="GET" style="flex: 1;">
                    <button type="submit" name="confirm" value="yes" class="btn btn-danger" style="width: 100%;">🗑️ YES, DELETE ALL DATA</button>
                </form>
                <a href="home.php" class="btn btn-cancel" style="text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">❌ Cancel</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// If confirmed, proceed with clearing database
try {
    // Get all tables
    $tables_result = mysqli_query($conn, "SHOW TABLES");
    $tables = [];
    while ($row = mysqli_fetch_array($tables_result)) {
        $tables[] = $row[0];
    }

    // Disable foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");

    // Truncate all tables
    $truncated_count = 0;
    foreach ($tables as $table) {
        mysqli_query($conn, "TRUNCATE TABLE `$table`");
        $truncated_count++;
    }

    // Re-enable foreign key checks
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");

    // Success page
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Cleared</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                margin: 50px auto;
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .success {
                background: #d4edda;
                border: 2px solid #28a745;
                color: #155724;
                padding: 20px;
                border-radius: 4px;
                margin-bottom: 20px;
                text-align: center;
            }
            .success h2 {
                margin-top: 0;
                color: #28a745;
            }
            .info {
                background: #e7f3ff;
                border: 1px solid #2196F3;
                color: #0056b3;
                padding: 15px;
                border-radius: 4px;
                margin-bottom: 20px;
                line-height: 1.6;
            }
            .btn {
                display: inline-block;
                padding: 12px 30px;
                background: #667eea;
                color: white;
                border-radius: 4px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s;
            }
            .btn:hover {
                background: #5568d3;
            }
            .tables-list {
                background: #f9f9f9;
                padding: 15px;
                border-radius: 4px;
                margin: 15px 0;
                max-height: 300px;
                overflow-y: auto;
            }
            .tables-list h4 {
                margin-top: 0;
            }
            .tables-list ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            .tables-list li {
                padding: 5px 0;
                border-bottom: 1px solid #eee;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="success">
                <h2>✅ DATABASE CLEARED SUCCESSFULLY!</h2>
            </div>

            <div class="info">
                <p><strong>✓ All data has been deleted from the database!</strong></p>
                <p>Total tables cleared: <strong><?php echo $truncated_count; ?></strong></p>
                
                <div class="tables-list">
                    <h4>Tables Truncated:</h4>
                    <ul>
                        <?php foreach ($tables as $table): ?>
                            <li>• <?php echo htmlspecialchars($table); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <p><strong>Next Steps:</strong></p>
                <ul style="text-align: left; display: inline-block;">
                    <li>All user accounts have been deleted</li>
                    <li>All student records have been deleted</li>
                    <li>All courses have been deleted</li>
                    <li>You can now run setup.php to re-initialize the database</li>
                </ul>
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="setup.php" class="btn">🔧 Run Setup to Re-Initialize Database</a>
                <br><br>
                <a href="home.php" class="btn" style="background: #6c757d;">← Back to Home</a>
            </div>
        </div>
    </body>
    </html>
    <?php

} catch (Exception $e) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Error</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #f5f5f5;
                padding: 20px;
            }
            .container {
                max-width: 600px;
                margin: 50px auto;
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .error {
                background: #f8d7da;
                border: 2px solid #dc3545;
                color: #721c24;
                padding: 20px;
                border-radius: 4px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="error">
                <h2>❌ ERROR</h2>
                <p><?php echo htmlspecialchars($e->getMessage()); ?></p>
                <p><a href="javascript:history.back()">← Go Back</a></p>
            </div>
        </div>
    </body>
    </html>
    <?php
}

mysqli_close($conn);
?>
