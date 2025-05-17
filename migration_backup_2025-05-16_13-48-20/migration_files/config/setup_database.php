<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';

// Create connection without database
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS livelihood_monitoring";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("livelihood_monitoring");

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS tbl_users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'member') NOT NULL,
    date_created DATETIME NOT NULL,
    date_updated DATETIME
)";

if ($conn->query($sql) === TRUE) {
    echo "Table tbl_users created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create household case records table
$sql = "CREATE TABLE IF NOT EXISTS tbl_household_case_records (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    household_head VARCHAR(255) NOT NULL,
    case_type VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    contact_number VARCHAR(20),
    family_size INT NOT NULL,
    monthly_income DECIMAL(10,2) NOT NULL,
    case_details TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    submitted_by INT NOT NULL,
    date_created DATETIME NOT NULL,
    date_updated DATETIME,
    FOREIGN KEY (submitted_by) REFERENCES tbl_users(user_id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table tbl_household_case_records created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Create default admin user if not exists
$admin_username = 'admin';
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$admin_fullname = 'System Administrator';
$admin_email = 'admin@example.com';

$stmt = $conn->prepare("INSERT IGNORE INTO tbl_users (username, password, full_name, email, role, date_created) 
                       VALUES (?, ?, ?, ?, 'admin', NOW())");
$stmt->bind_param("ssss", $admin_username, $admin_password, $admin_fullname, $admin_email);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "Default admin user created successfully<br>";
    } else {
        echo "Admin user already exists<br>";
    }
} else {
    echo "Error creating admin user: " . $stmt->error . "<br>";
}

$conn->close();
echo "Database setup completed!";
?> 