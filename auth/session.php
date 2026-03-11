<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine base path dynamically (works regardless of subfolder name)
$base_path = str_replace('auth', '', dirname($_SERVER['SCRIPT_NAME']));
$login_url = rtrim($base_path, '/') . '/index.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Log session redirect
    @file_put_contents(__DIR__ . "/../logs/redirect_debug.log",
        date('c') . " SESSION: no user_id; redirecting to home\n", FILE_APPEND);
    header("Location: " . rtrim($base_path, '/') . "/home.php");
    exit();
}

// Optional: Check session timeout (30 minutes)
$timeout_duration = 1800; // 30 minutes
if (isset($_SESSION['last_activity'])) {
    if ((time() - $_SESSION['last_activity']) > $timeout_duration) {
        session_destroy();
        @file_put_contents(__DIR__ . "/../logs/redirect_debug.log",
            date('c') . " SESSION: expired for user_id=" . ($_SESSION['user_id'] ?? 'NONE') . "\n", FILE_APPEND);
        // Redirect to home with session expired message
        header("Location: " . rtrim($base_path, '/') . "/home.php?msg=Session+expired");
        exit();
    }
}
$_SESSION['last_activity'] = time();
?>
