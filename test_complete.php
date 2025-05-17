<?php
// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database connection
echo "Testing database connection...\n";
try {
    $conn = new mysqli(
        $_ENV['DB_HOST'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'],
        $_ENV['DB_NAME']
    );
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    echo "Database connection successful!\n";
    
    // Create tables
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE IF NOT EXISTS livelihood_programs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE IF NOT EXISTS applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        program_id INT NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (program_id) REFERENCES livelihood_programs(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    if ($conn->multi_query($sql)) {
        echo "Database tables created successfully!\n";
    } else {
        echo "Error creating tables: " . $conn->error . "\n";
    }
    
    // Insert test data
    $password = password_hash('test123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, first_name, last_name) 
            VALUES ('testuser', 'test@example.com', '$password', 'Test', 'User')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Test user created successfully!\n";
    } else {
        echo "Error creating test user: " . $conn->error . "\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

// Test email configuration
echo "\nTesting email configuration...\n";
try {
    // Load SwiftMailer classes
    require_once __DIR__ . '/vendor/swiftmailer/swiftmailer/lib/swift_required.php';
    
    // Create the Transport
    $transport = (new Swift_SmtpTransport(
        $_ENV['SMTP_HOST'],
        $_ENV['SMTP_PORT'],
        'tls'
    ))
    ->setUsername($_ENV['SMTP_USER'])
    ->setPassword($_ENV['SMTP_PASS']);

    // Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

    // Create a message
    $message = (new Swift_Message('Test Email Configuration'))
        ->setFrom([$_ENV['SMTP_FROM'] => 'MSWD Livelihood System'])
        ->setTo([$_ENV['SMTP_USER']])
        ->setBody(
            '<h1>Email Configuration Test</h1>' .
            '<p>This is a test email to verify the email configuration.</p>' .
            '<p>Date: ' . date('Y-m-d H:i:s') . '</p>',
            'text/html'
        );

    // Send the message
    $result = $mailer->send($message);
    
    if ($result) {
        echo "Email sent successfully!\n";
    } else {
        echo "Failed to send email.\n";
    }
    
} catch (Exception $e) {
    echo "Email error: " . $e->getMessage() . "\n";
}

// Test file upload directory
echo "\nTesting file upload directory...\n";
$upload_dir = $_ENV['UPLOAD_DIR'];
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
    echo "Upload directory created: $upload_dir\n";
} else {
    echo "Upload directory exists: $upload_dir\n";
}

// Test session configuration
echo "\nTesting session configuration...\n";
session_start();
$_SESSION['test'] = 'session_test';
if (isset($_SESSION['test'])) {
    echo "Session test successful!\n";
} else {
    echo "Session test failed.\n";
}

// Test file upload size limit
echo "\nTesting file upload size limit...\n";
$max_size = $_ENV['MAX_UPLOAD_SIZE'];
echo "Maximum upload size: " . ($max_size / 1024 / 1024) . "MB\n";

// Test allowed file types
echo "\nTesting allowed file types...\n";
$allowed_types = explode(',', $_ENV['ALLOWED_FILE_TYPES']);
echo "Allowed file types: " . implode(', ', $allowed_types) . "\n";

// Test password salting
echo "\nTesting password salting...\n";
$password = password_hash('testpassword', PASSWORD_DEFAULT, ['cost' => $_ENV['PASSWORD_SALT_ROUNDS']]);
if (password_verify('testpassword', $password)) {
    echo "Password salting test successful!\n";
} else {
    echo "Password salting test failed.\n";
}

// Test JWT (if needed)
echo "\nTesting JWT...\n";
try {
    require_once __DIR__ . '/vendor/firebase/php-jwt/src/JWT.php';
    
    $token = JWT::encode([
        'iss' => $_ENV['BASE_URL'],
        'iat' => time(),
        'exp' => time() + (60 * 60), // 1 hour
        'data' => [
            'user' => 'test_user'
        ]
    ], $_ENV['JWT_SECRET']);
    
    echo "JWT test successful!\n";
} catch (Exception $e) {
    echo "JWT test failed: " . $e->getMessage() . "\n";
}
?>
