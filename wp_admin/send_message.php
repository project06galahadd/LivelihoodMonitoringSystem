<?php
session_start();
require_once "includes/conn.php";

// Check if user is logged in as admin
if (!isset($_SESSION['admin']) || trim($_SESSION['admin']) == '') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Get admin user info
$sql = "SELECT * FROM tbl_users WHERE ID = '".$_SESSION['admin']."'";
$query = $conn->query($sql);
if($query->num_rows > 0) {
    $user = $query->fetch_assoc();
    $sender_id = $user['ID'];
} else {
    echo json_encode(['success' => false, 'message' => 'Admin user not found']);
    exit();
}

// Check if required fields are present
if (!isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$receiver_id = $_POST['receiver_id'];
$message = trim($_POST['message']);

// Check if message is not empty
if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit();
}

// Verify that receiver is a member
$check_receiver = $conn->prepare("SELECT ID FROM tbl_users WHERE ID = ? AND ROLE = 'MEMBER'");
$check_receiver->bind_param("i", $receiver_id);
$check_receiver->execute();
$result = $check_receiver->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid receiver']);
    exit();
}

// Insert the message
$stmt = $conn->prepare("INSERT INTO tbl_chat_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
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
            'created_at' => $created_at
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error sending message']);
}

$stmt->close();
$check_receiver->close();
$conn->close(); 