<?php
require_once 'auth-config.php';

session_name($ADMIN_CONFIG['session_name']);
session_start();

// Destroy session
$_SESSION = array();

// Delete session cookie
if (isset($_COOKIE[$ADMIN_CONFIG['session_name']])) {
    setcookie($ADMIN_CONFIG['session_name'], '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Redirect to login
header('Location: login.php');
exit();
?>
