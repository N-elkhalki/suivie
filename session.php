<?php
session_start();

$timeout_duration = 1800; // 30 minutes, expressed in seconds

// Check if the user is logged in
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: Loginpage.php");
    exit;
}

// Check if the timeout duration has passed since the last activity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // If so, destroy the session and redirect to the login page
    session_unset();
    session_destroy();
    header("Location: Loginpage.php");
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Check if session is not set or empty
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    // Redirect the user to the login page or display an error message
    header("Location: Loginpage.php");
    exit;
}

?>