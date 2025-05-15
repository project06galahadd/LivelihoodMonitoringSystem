<?php
session_start();
require_once "../wp_admin/includes/conn.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Get and validate input
$user_id = $_SESSION['user_id'];
$fullname = sanitize_input($_POST['fullname'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$contact = sanitize_input($_POST['contact'] ?? '');
$address = sanitize_input($_POST['address'] ?? '');

// Validate required fields
if (empty($fullname) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Full name and email are required']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    // Check if email is already taken by another user
    $stmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE email = ? AND user_id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email is already taken']);
        exit;
    }
    
    // Update user profile
    $stmt = $conn->prepare("UPDATE tbl_users SET fullname = ?, email = ?, contact = ?, address = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $fullname, $email, $contact, $address, $user_id);
    
    if ($stmt->execute()) {
        // Update session variables
        $_SESSION['fullname'] = $fullname;
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        throw new Exception("Error updating profile");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating profile: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?> 