<?php
session_start();
require_once "../wp_admin/includes/conn.php";

// Check if user is logged in and has member role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'MEMBER') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if admin_id is provided
if (!isset($_GET['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing admin ID']);
    exit();
}

$user_id = $_SESSION['user_id'];
$admin_id = (int)$_GET['admin_id'];
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

try {
    // Verify admin exists and is active
    $check_sql = "SELECT ID FROM tbl_users WHERE ID = ? AND ROLE = 'ADMIN' AND ACC_STATUS = 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $admin_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid admin or account is inactive']);
        exit();
    }

    // Get messages
    $sql = "SELECT m.*, 
            DATE_FORMAT(m.created_at, '%M %d, %Y %h:%i %p') as created_at,
            CASE 
                WHEN m.sender_id = ? THEN 1 
                ELSE 0 
            END as is_sent
            FROM tbl_chat_messages m 
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) 
            OR (m.sender_id = ? AND m.receiver_id = ?))
            AND m.message_id > ?
            ORDER BY m.created_at ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiii", $user_id, $user_id, $admin_id, $admin_id, $user_id, $last_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    // Mark received messages as read
    if (!empty($messages)) {
        $update_sql = "UPDATE tbl_chat_messages 
                      SET is_read = 1 
                      WHERE receiver_id = ? AND sender_id = ? AND is_read = 0";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $user_id, $admin_id);
        $update_stmt->execute();
    }
    
    // Get unread count
    $unread_sql = "SELECT COUNT(*) as unread_count 
                   FROM tbl_chat_messages 
                   WHERE receiver_id = ? AND sender_id = ? AND is_read = 0";
    $unread_stmt = $conn->prepare($unread_sql);
    $unread_stmt->bind_param("ii", $user_id, $admin_id);
    $unread_stmt->execute();
    $unread_count = $unread_stmt->get_result()->fetch_assoc()['unread_count'];
    
    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'unread_count' => $unread_count
    ]);
} catch (Exception $e) {
    error_log("Error in get_messages.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error retrieving messages. Please try again.']);
}
?> 