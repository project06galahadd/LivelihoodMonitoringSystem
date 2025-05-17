<?php
// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Security first
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Database Configuration
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);
define('DB_NAME', $_ENV['DB_NAME']);

// Application Configuration
define('APP_NAME', $_ENV['APP_NAME']);
define('APP_VERSION', $_ENV['APP_VERSION']);
define('BASE_URL', $_ENV['BASE_URL']);

// Security Settings
define('SECRET_KEY', $_ENV['SECRET_KEY']);
define('JWT_SECRET', $_ENV['JWT_SECRET']);
define('PASSWORD_SALT_ROUNDS', (int)$_ENV['PASSWORD_SALT_ROUNDS']);

// File Upload Settings
define('UPLOAD_DIR', __DIR__ . '/../' . $_ENV['UPLOAD_DIR']);
define('MAX_UPLOAD_SIZE', (int)$_ENV['MAX_UPLOAD_SIZE']);
define('ALLOWED_FILE_TYPES', explode(',', $_ENV['ALLOWED_FILE_TYPES']));

// Email Configuration
define('SMTP_HOST', $_ENV['SMTP_HOST']);
define('SMTP_PORT', (int)$_ENV['SMTP_PORT']);
define('SMTP_USER', $_ENV['SMTP_USER']);
define('SMTP_PASS', $_ENV['SMTP_PASS']);
define('SMTP_FROM', $_ENV['SMTP_FROM']);

// Session Settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', (int)$_ENV['SESSION_COOKIE_SECURE']);
ini_set('session.cookie_samesite', 'Lax');

// Initialize session
session_start();

// Database Connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection failed");
}

// Set timezone
date_default_timezone_set('Asia/Manila');

// Error handling
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile on line $errline");
    return true;
});

// Exception handling
set_exception_handler(function($exception) {
    error_log("Exception: " . $exception->getMessage());
    die("An unexpected error occurred");
});

// Helper functions
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length/2));
}
