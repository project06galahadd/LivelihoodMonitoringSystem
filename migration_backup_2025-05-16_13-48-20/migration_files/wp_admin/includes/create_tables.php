// Create tbl_livelihood_records table
$sql = "CREATE TABLE IF NOT EXISTS tbl_livelihood_records (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    program_name VARCHAR(255) NOT NULL,
    program_type VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    status ENUM('active', 'completed', 'on-hold', 'cancelled') DEFAULT 'active',
    progress INT DEFAULT 0,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table tbl_livelihood_records created successfully<br>";
} else {
    echo "Error creating table tbl_livelihood_records: " . $conn->error . "<br>";
}

// Create tbl_household_records table
$sql = "CREATE TABLE IF NOT EXISTS tbl_household_records (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    household_head VARCHAR(255) NOT NULL,
    case_type VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    contact_number VARCHAR(20),
    family_size INT,
    monthly_income DECIMAL(10,2),
    status ENUM('active', 'resolved', 'on-hold', 'referred') DEFAULT 'active',
    case_details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table tbl_household_records created successfully<br>";
} else {
    echo "Error creating table tbl_household_records: " . $conn->error . "<br>";
} 