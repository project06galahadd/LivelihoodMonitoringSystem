<?php
// Load environment variables
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database connection
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
    
    echo "Database connection successful!";
    
    // Test creating a table
    $sql = "CREATE TABLE IF NOT EXISTS test_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "\nTest table created successfully";
    } else {
        echo "\nError creating table: " . $conn->error;
    }
    
    // Insert test data
    $sql = "INSERT INTO test_table (name) VALUES ('Test Entry')";
    if ($conn->query($sql) === TRUE) {
        echo "\nTest data inserted successfully";
    } else {
        echo "\nError inserting data: " . $conn->error;
    }
    
    // Select data
    $sql = "SELECT id, name, created_at FROM test_table";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "\n\nDatabase contains test data:";
        while($row = $result->fetch_assoc()) {
            echo "\nID: " . $row["id"] . " - Name: " . $row["name"] . " - Created: " . $row["created_at"];
        }
    } else {
        echo "\nNo results";
    }
    
    $conn->close();
    echo "\n\nDatabase connection test completed successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
