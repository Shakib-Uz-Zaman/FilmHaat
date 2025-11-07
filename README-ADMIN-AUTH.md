# 🔐 Admin Authentication System - Complete Guide

## System Overview

This authentication system is highly secure and works without a database. No one can bypass it using view-source or browser console.

## 🛡️ Security Features

1. ✅ **Password Hashing (bcrypt)** - Passwords are stored in encrypted form
2. ✅ **Server-Side Validation** - All checking happens on the server
3. ✅ **Session Security** - HTTPOnly cookies with secure settings
4. ✅ **CSRF Protection** - Form submission security
5. ✅ **Brute Force Protection** - 15 minute lockout after 5 failed attempts
6. ✅ **Session Timeout** - Auto logout after 1 hour
7. ✅ **No Database Required** - Only PHP files

## 📁 Files Created

### 1. `auth-config.php`
- Admin credentials and security settings
- Password hash and security configuration
- **Never make this file public!**

### 2. `auth-check.php`
- Must be included in every protected page
- Automatic session verification
- Auto-redirect if not authenticated

### 3. `login.php`
- Admin login page
- Beautiful UI with English language
- Includes brute force protection

### 4. `logout.php`
- Secure logout functionality
- Destroys session

### 5. `password-generator.php`
- For creating new password hash
- **Delete after use!**

## 🚀 Default Login Credentials

```
Username: admin
Password: admin123
```

**⚠️ Important: Change these credentials after first login!**

## 🔧 How to Change Password

### Method 1: Using password-generator.php (Easy)

1. Open `password-generator.php` in browser
2. Enter your new password
3. Click "Generate Hash" button
4. Copy the generated hash
5. Open `auth-config.php`
6. Replace `password_hash` value with new hash
7. Save the file
8. **Delete the `password-generator.php` file!**

### Method 2: Using PHP command line

Run this command in terminal:

```bash
php -r "echo password_hash('your_new_password', PASSWORD_BCRYPT);"
```

Copy the output hash and paste it in `auth-config.php`.

## 🔒 Protected Pages

Currently protected:
- ✅ `config-manager.php` - Full admin panel
- ✅ `config-api.php` - Configuration API (POST requests only)

### To protect a new page

Add this line at the beginning of any PHP file:

```php
<?php
require_once 'auth-check.php';
// Rest of the code...
?>
```

## 📊 Changing Security Settings

You can change these settings in `auth-config.php`:

```php
$ADMIN_CONFIG = [
    'username' => 'admin',              // Change username
    'password_hash' => '...',           // Password hash
    'session_lifetime' => 3600,         // Session timeout (seconds) - default 1 hour
    'max_login_attempts' => 5,          // Maximum login attempts
    'lockout_duration' => 900,          // Lockout time (seconds) - default 15 minutes
];
```

## 🎯 How to Use

### 1. First Time Login

1. Open `login.php` in browser
2. Username: `admin`
3. Password: `admin123`
4. Click "Login" button

### 2. Config Manager Access

After login, you will be automatically redirected to `config-manager.php`.

### 3. Logout

Click the "🚪 Logout" button at the top of Config Manager.

## ⚠️ Security Warnings

1. **Change Password**: Must change the default password
2. **Delete password-generator.php**: Must delete after use
3. **Keep auth-config.php secure**: Never make this file public
4. **Use HTTPS**: Enable HTTPS in production

## 🧪 Testing

1. Visit `login.php`
2. Try 5 times with wrong password - will be locked out
3. Login with correct credentials
4. Access `config-manager.php` - it will work
5. Logout
6. Try to directly access `config-manager.php` - will redirect to `login.php`

## 🐛 Troubleshooting

### Problem: Keeps showing "Session expired"
**Solution**: Increase `session_lifetime` in `auth-config.php` (example: 7200 for 2 hours)

### Problem: Not redirecting after login
**Solution**: Check if session cookies are enabled in browser

### Problem: Old password still works after changing
**Solution**: Clear browser cache and logout then login again

## 💡 Tips

- Use a strong password (uppercase, lowercase, numbers, symbols)
- Also change username from default "admin"
- Change password at regular intervals
- Set session lifetime as per your requirement

## 🎉 Done!

Now your Config Manager is completely secure! Only admin will have access. 🔒
