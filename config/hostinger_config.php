<?php
// Hostinger Configuration
define('HOSTINGER_MODE', true);

// Database Configuration
define('DB_HOST', 'localhost'); // Update with Hostinger's database host
define('DB_USER', ''); // Update with your Hostinger database username
define('DB_PASS', ''); // Update with your Hostinger database password
define('DB_NAME', ''); // Update with your Hostinger database name

// Application Configuration
define('BASE_URL', 'https://your-domain.com'); // Update with your domain
define('UPLOAD_PATH', '/home/username/public_html/uploads'); // Update with your Hostinger path
define('VIDEO_PATH', '/home/username/public_html/videos'); // Update with your Hostinger path

// Security Configuration
define('SESSION_LIFETIME', 3600); // 1 hour
define('SECURE_COOKIES', true);
define('ALLOWED_ORIGINS', ['https://your-domain.com']); // Update with your domain

// Error Reporting
if (HOSTINGER_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', '/home/username/logs/php_errors.log'); // Update with your Hostinger path
}
?> 