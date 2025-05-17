<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../wp_admin/includes/conn.php";

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read and execute the SQL file
$sql = file_get_contents(__DIR__ . '/setup.sql');

try {
    // Enable multiple statement execution
    $conn->multi_query($sql);
    
    // Process all results
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    
    if ($conn->error) {
        throw new Exception("Error executing SQL: " . $conn->error);
    }
    
    echo "Database setup completed successfully!";
} catch (Exception $e) {
    die("Setup failed: " . $e->getMessage());
}

// Close connection
$conn->close(); 