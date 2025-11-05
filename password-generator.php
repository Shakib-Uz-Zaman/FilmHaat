<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    if (empty($password)) {
        $error = 'Password cannot be empty';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $generated = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator</title>
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

        .container {
            width: 100%;
            max-width: 600px;
            padding: 20px;
        }

        h1 {
            color: #1a1a1a;
            font-size: 28px;
            font-weight: 500;
            margin-bottom: 24px;
            letter-spacing: -0.5px;
            text-align: center;
        }

        .warning {
            background: #fff4e6;
            color: #e67e22;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .warning strong {
            display: block;
            margin-bottom: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #1a1a1a;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input[type="text"] {
            width: 100%;
            padding: 16px 20px;
            border: none;
            background: #e8e8e8;
            font-size: 15px;
            color: #1a1a1a;
            border-radius: 8px;
            transition: background 0.2s ease;
        }

        input[type="text"]::placeholder {
            color: #888;
        }

        input[type="text"]:focus {
            outline: none;
            background: #ddd;
        }

        button[type="submit"] {
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
        }

        button[type="submit"]:hover {
            background: linear-gradient(90deg, #f31447 0%, #d13557 100%);
            opacity: 0.9;
        }

        button[type="submit"]:active {
            opacity: 1;
        }

        .result {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 20px;
            border-radius: 8px;
            margin-top: 24px;
        }

        .result strong {
            display: block;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .hash-value {
            background: white;
            padding: 12px;
            border-radius: 6px;
            word-break: break-all;
            font-family: monospace;
            font-size: 13px;
            margin-top: 8px;
            color: #1a1a1a;
        }

        .copy-button {
            margin-top: 12px;
            padding: 10px 20px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .copy-button:hover {
            background: #1b5e20;
        }

        .instructions {
            background: #e3f2fd;
            color: #1565c0;
            padding: 16px;
            border-radius: 8px;
            margin-top: 16px;
            font-size: 14px;
        }

        .instructions strong {
            display: block;
            margin-bottom: 8px;
        }

        .instructions ol {
            margin-left: 20px;
            margin-top: 8px;
        }

        .instructions li {
            margin: 6px 0;
        }

        .error {
            background: #fee;
            color: #c00;
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Password Hash Generator</h1>
        
        <div class="warning">
            <strong>Security Warning!</strong>
            Delete this file after use. It is not safe to keep it in public access.
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="password">Enter your new password:</label>
                <input 
                    type="text" 
                    id="password" 
                    name="password" 
                    required 
                    placeholder="Your strong password"
                    autocomplete="off"
                >
            </div>
            
            <button type="submit">Generate Hash</button>
        </form>

        <?php if (isset($generated) && $generated): ?>
            <div class="result">
                <strong>Password Hash Generated Successfully!</strong>
                <div>Your password: <code><?php echo htmlspecialchars($password); ?></code></div>
                <div class="hash-value" id="hashValue"><?php echo htmlspecialchars($hash); ?></div>
                <button class="copy-button" onclick="copyHash()">Copy Hash</button>
            </div>

            <div class="instructions">
                <strong>Next Steps:</strong>
                <ol>
                    <li>Copy the hash above</li>
                    <li>Open the <code>auth-config.php</code> file</li>
                    <li>Replace the <code>password_hash</code> value with this new hash</li>
                    <li>You can also change the <code>username</code> if you want</li>
                    <li>Save the file</li>
                    <li><strong>Delete this <code>password-generator.php</code> file!</strong></li>
                </ol>
            </div>
        <?php elseif (isset($error)): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function copyHash() {
            const hashValue = document.getElementById('hashValue').textContent;
            navigator.clipboard.writeText(hashValue).then(() => {
                alert('✅ Hash copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy:', err);
                const range = document.createRange();
                range.selectNode(document.getElementById('hashValue'));
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
            });
        }
    </script>
</body>
</html>
