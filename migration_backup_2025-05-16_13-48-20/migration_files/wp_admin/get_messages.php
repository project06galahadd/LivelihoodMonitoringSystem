<?php
session_start();
require_once "includes/conn.php";

// Check if user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if member_id is provided
if (!isset($_GET['member_id'])) {
    echo json_encode(['success' => false, 'message' => 'Member ID is required']);
    exit();
}

$member_id = $_GET['member_id'];
$last_id = isset($_GET['last_id']) ? $_GET['last_id'] : 0;

// Get messages
$query = "SELECT m.*, u.FIRSTNAME, u.LASTNAME 
          FROM tbl_chat_messages m 
          JOIN tbl_users u ON m.sender_id = u.ID 
          WHERE ((m.sender_id = ? AND m.receiver_id = ?) 
          OR (m.sender_id = ? AND m.receiver_id = ?))
          AND m.message_id > ?
          ORDER BY m.created_at ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("iiiii", $user_id, $member_id, $member_id, $user_id, $last_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    // Format the timestamp
    $created_at = new DateTime($row['created_at']);
    $row['created_at'] = $created_at->format('M d, Y h:i A');
    
    $messages[] = [
        'message_id' => $row['message_id'],
        'sender_id' => $row['sender_id'],
        'receiver_id' => $row['receiver_id'],
        'message' => $row['message'],
        'created_at' => $row['created_at'],
        'is_read' => $row['is_read']
    ];
}

// Mark received messages as read
$update_query = "UPDATE tbl_chat_messages 
                SET is_read = 1 
                WHERE sender_id = ? 
                AND receiver_id = ? 
                AND is_read = 0";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("ii", $member_id, $user_id);
$update_stmt->execute();

echo json_encode([
    'success' => true,
    'messages' => $messages
]);

$stmt->close();
$update_stmt->close();
$conn->close(); 