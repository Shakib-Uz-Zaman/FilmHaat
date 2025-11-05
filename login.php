<?php
require_once 'auth-config.php';

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Strict');

session_name($ADMIN_CONFIG['session_name']);
session_start();

if (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true) {
    header('Location: config-manager.php');
    exit();
}

$error_message = '';
$lockout_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error_message = 'Security verification failed. Please try again.';
    } else {
        $attempt_check = checkLoginAttempts();
        
        if (!$attempt_check['allowed']) {
            $lockout_message = 'Too many failed attempts. Please try again after ' . $attempt_check['remaining_time'] . ' minutes.';
        } else {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (verifyAdminPassword($username, $password)) {
                session_regenerate_id(true);
                $_SESSION['admin_authenticated'] = true;
                $_SESSION['admin_username'] = $username;
                $_SESSION['last_activity'] = time();
                $_SESSION['created'] = time();
                resetLoginAttempts();
                
                header('Location: config-manager.php');
                exit();
            } else {
                recordFailedAttempt();
                $error_message = 'Incorrect username or password.';
                
                $attempt_check = checkLoginAttempts();
                if (!$attempt_check['allowed']) {
                    $lockout_message = 'Too many failed attempts. Please try again after ' . $attempt_check['remaining_time'] . ' minutes.';
                    $error_message = '';
                }
            }
        }
    }
}

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'session_expired') {
        $error_message = 'Your session has expired. Please login again.';
    } elseif ($_GET['error'] === 'invalid_session') {
        $error_message = 'Invalid session. Please login again.';
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/webp" href="attached_image/logo-image.webp">
    <link rel="apple-touch-icon" href="attached_image/logo-image.webp">
    <title>Admin Login - FilmHaat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
        }

        h1 {
            color: #1a1a1a;
            font-size: 28px;
            font-weight: 500;
            margin-bottom: 40px;
            letter-spacing: -0.5px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 16px 20px;
            border: none;
            background: #e8e8e8;
            font-size: 15px;
            color: #1a1a1a;
            border-radius: 8px;
            transition: background 0.2s ease;
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #888;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            background: #ddd;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password svg {
            width: 24px;
            height: 24px;
            stroke: #1a1a1a;
            fill: none;
            stroke-width: 2;
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(90deg, #df0033 0%, #bd284b 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease, opacity 0.2s ease;
            margin-top: 12px;
        }

        .login-button:hover {
            background: linear-gradient(90deg, #f31447 0%, #d13557 100%);
            opacity: 0.9;
        }

        .login-button:active {
            opacity: 1;
        }

        .login-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .error-message {
            background: #fee;
            color: #c00;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .lockout-message {
            background: #fff4e6;
            color: #e67e22;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Log in to FilmHaat</h1>

        <?php if ($lockout_message): ?>
            <div class="lockout-message">
                <?php echo htmlspecialchars($lockout_message); ?>
            </div>
        <?php elseif ($error_message): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <div class="form-group">
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="EMAIL OR USERNAME"
                    required 
                    autocomplete="username"
                    <?php echo $lockout_message ? 'disabled' : ''; ?>
                >
            </div>

            <div class="form-group">
                <div class="password-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="PASSWORD"
                        required 
                        autocomplete="current-password"
                        <?php echo $lockout_message ? 'disabled' : ''; ?>
                    >
                    <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Toggle password visibility">
                        <svg id="eyeIcon" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="login-button" <?php echo $lockout_message ? 'disabled' : ''; ?>>
                Log In
            </button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
