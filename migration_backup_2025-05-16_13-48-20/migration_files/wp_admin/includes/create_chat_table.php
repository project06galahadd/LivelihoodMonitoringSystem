<?php
require_once "conn.php";

// Drop the table if it exists to recreate it
$drop_table = "DROP TABLE IF EXISTS tbl_chat_messages";
$conn->query($drop_table);

// Create chat messages table
$create_table_query = "CREATE TABLE tbl_chat_messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    FOREIGN KEY (sender_id) REFERENCES tbl_users(ID) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES tbl_users(ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($create_table_query)) {
    echo "Chat messages table created successfully";
} else {
    echo "Error creating chat messages table: " . $conn->error;
}

// Close the connection
$conn->close();
?> 