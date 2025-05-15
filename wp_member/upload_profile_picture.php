<?php
session_start();
require_once "../wp_admin/includes/conn.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
    $error_message = isset($_FILES['profile_picture']) ? 
                    'Upload error: ' . $_FILES['profile_picture']['error'] : 
                    'No file uploaded';
    echo json_encode(['success' => false, 'message' => $error_message]);
    exit;
}

$file = $_FILES['profile_picture'];
$user_id = $_SESSION['user_id'];

// Validate file type
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG and GIF are allowed']);
    exit;
}

// Validate file size (max 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File size too large. Maximum size is 5MB']);
    exit;
}

// Create uploads directory if it doesn't exist
$upload_dir = '../uploads/profile';
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
        exit;
    }
    chmod($upload_dir, 0777);
}

// Generate unique filename
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
$filepath = $upload_dir . '/' . $filename;

try {
    // Get old profile picture
    $stmt = $conn->prepare("SELECT profile_picture FROM tbl_users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $old_picture = $result->fetch_assoc();

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception("Failed to move uploaded file");
    }

    // Update database with new profile picture
    $stmt = $conn->prepare("UPDATE tbl_users SET profile_picture = ? WHERE user_id = ?");
    $stmt->bind_param("si", $filename, $user_id);
    
    if ($stmt->execute()) {
        // Delete old profile picture if exists
        if ($old_picture && $old_picture['profile_picture']) {
            $old_file = $upload_dir . '/' . $old_picture['profile_picture'];
            if (file_exists($old_file)) {
                unlink($old_file);
            }
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Profile picture updated successfully',
            'filename' => $filename
        ]);
    } else {
        // Delete uploaded file if database update fails
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        throw new Exception("Error updating database");
    }
} catch (Exception $e) {
    // Clean up uploaded file if there's an error
    if (file_exists($filepath)) {
        unlink($filepath);
    }
    echo json_encode(['success' => false, 'message' => 'Error updating profile picture: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?> 