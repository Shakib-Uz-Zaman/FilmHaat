<?php
// Admin Authentication Configuration
// DO NOT share this file or commit to public repositories

// Admin credentials - Change these after first login
$ADMIN_CONFIG = [
    'username' => 'admin',
    // Password: admin123 (Change this immediately after first login)
    // To generate new password hash, use: password_hash('your_password', PASSWORD_BCRYPT)
    'password_hash' => '$2y$12$yIYG4Dep652pGE6LRzJylufO94GMZm81i2wqqrNJSJzao33T/rxcS',
    
    // Session security settings
    'session_name' => 'FILMHAAT_ADMIN_SESSION',
    'session_lifetime' => 3600, // 1 hour in seconds
    
    // Brute force protection
    'max_login_attempts' => 5,
    'lockout_duration' => 900, // 15 minutes in seconds
];

// Password verification function
function verifyAdminPassword($username, $password) {
    global $ADMIN_CONFIG;
    
    if ($username !== $ADMIN_CONFIG['username']) {
        return false;
    }
    
    return password_verify($password, $ADMIN_CONFIG['password_hash']);
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Check login attempts
function checkLoginAttempts() {
    global $ADMIN_CONFIG;
    
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = 0;
    }
    
    // Reset attempts if lockout period has passed
    if (time() - $_SESSION['last_attempt_time'] > $ADMIN_CONFIG['lockout_duration']) {
        $_SESSION['login_attempts'] = 0;
    }
    
    // Check if locked out
    if ($_SESSION['login_attempts'] >= $ADMIN_CONFIG['max_login_attempts']) {
        $remaining_time = $ADMIN_CONFIG['lockout_duration'] - (time() - $_SESSION['last_attempt_time']);
        if ($remaining_time > 0) {
            return [
                'allowed' => false,
                'remaining_time' => ceil($remaining_time / 60) // in minutes
            ];
        }
    }
    
    return ['allowed' => true];
}

// Record failed login attempt
function recordFailedAttempt() {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();
}

// Reset login attempts on successful login
function resetLoginAttempts() {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}

?>
