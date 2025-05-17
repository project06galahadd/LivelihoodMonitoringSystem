<?php
// Database configuration
$host = DB_HOST;
$username = DB_USER;
$password = DB_PASS;
$database = DB_NAME;

// Create connection without database first
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if database exists, if not create it
$result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
if ($result->num_rows == 0) {
    // Database doesn't exist, create it
    if ($conn->query("CREATE DATABASE $database")) {
        // Select the new database
        $conn->select_db($database);
        
        // Create required tables
        // Users table
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
        $conn->query($sql);

        // Case records table
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
        $conn->query($sql);

        // Create default admin user
        $admin_username = 'admin';
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $admin_fullname = 'System Administrator';
        $admin_email = 'admin@example.com';

        $stmt = $conn->prepare("INSERT IGNORE INTO tbl_users (username, password, full_name, email, role, date_created) 
                               VALUES (?, ?, ?, ?, 'admin', NOW())");
        $stmt->bind_param("ssss", $admin_username, $admin_password, $admin_fullname, $admin_email);
        $stmt->execute();
    } else {
        die("Error creating database: " . $conn->error);
    }
} else {
    // Database exists, select it
    $conn->select_db($database);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
?> 