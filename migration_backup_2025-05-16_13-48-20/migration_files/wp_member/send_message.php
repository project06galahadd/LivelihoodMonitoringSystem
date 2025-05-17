<?php
session_start();
require_once "../wp_admin/includes/conn.php";

// Check if user is logged in and has member role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'MEMBER') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if required fields are present
if (!isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = (int)$_POST['receiver_id'];
$message = trim($_POST['message']);

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit();
}

try {
    // Verify receiver is an admin
    $check_sql = "SELECT ID, FIRSTNAME, LASTNAME FROM tbl_users WHERE ID = ? AND ROLE = 'ADMIN' AND ACC_STATUS = 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $receiver_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid receiver or admin account is inactive']);
        exit();
    }

    $admin = $check_result->fetch_assoc();

    // Insert message
    $sql = "INSERT INTO tbl_chat_messages (sender_id, receiver_id, message, is_read, created_at) VALUES (?, ?, ?, 0, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    
    if ($stmt->execute()) {
        $message_id = $conn->insert_id;
        $created_at = date('M d, Y h:i A');
        
        echo json_encode([
            'success' => true,
            'message' => [
                'message_id' => $message_id,
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'message' => $message,
                'created_at' => $created_at,
                'is_read' => 0
            ]
        ]);
    } else {
        throw new Exception("Error sending message");
    }
} catch (Exception $e) {
    error_log("Error in send_message.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error sending message. Please try again.']);
}
?> 