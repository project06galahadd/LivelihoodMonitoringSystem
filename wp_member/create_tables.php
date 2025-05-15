<?php
require_once '../config/database.php';

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
    echo "Table tbl_household_case_records created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?> 