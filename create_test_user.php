<?php
$conn = new mysqli('localhost', 'root', '', 'livelihood_database', null, '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create users table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}

// Create test user
$username = 'user';
$password = 'member';
$role = 'MEMBER';

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the user
$sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $hashed_password, $role);

if ($stmt->execute()) {
    echo "Test user created successfully!";
} else {
    echo "Error creating test user: " . $conn->error;
}

$stmt->close();
$conn->close();
?> 