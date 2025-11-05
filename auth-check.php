<?php
// Authentication Check - Include this file to protect admin pages
// This file verifies that the user is authenticated before allowing access

require_once 'auth-config.php';

// Configure secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Start session with custom name
session_name($ADMIN_CONFIG['session_name']);
session_start();

// Regenerate session ID periodically for security
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Check if user is authenticated
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    // Not authenticated - redirect to login
    header('Location: login.php');
    exit();
}

// Check session timeout
if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    if ($inactive_time > $ADMIN_CONFIG['session_lifetime']) {
        // Session expired
        session_unset();
        session_destroy();
        header('Location: login.php?error=session_expired');
        exit();
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Verify session username matches
if (!isset($_SESSION['admin_username']) || $_SESSION['admin_username'] !== $ADMIN_CONFIG['username']) {
    session_unset();
    session_destroy();
    header('Location: login.php?error=invalid_session');
    exit();
}

?>
