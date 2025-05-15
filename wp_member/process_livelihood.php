<?php
session_start();
require_once "../wp_admin/includes/conn.php";

// Check if user is logged in and has member role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'MEMBER') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if all required fields are present
if (!isset($_POST['program_id']) || !isset($_POST['experience']) || 
    !isset($_POST['current_situation']) || !isset($_POST['willing_training']) || 
    !isset($_POST['reason'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

$user_id = $_SESSION['user_id'];
$program_id = $_POST['program_id'];
$experience = $_POST['experience'];
$current_situation = $_POST['current_situation'];
$willing_training = $_POST['willing_training'];
$reason = $_POST['reason'];

try {
    // Check if user already has a pending application for this program
    $check_sql = "SELECT * FROM tbl_livelihood_applications 
                  WHERE user_id = ? AND livelihood_id = ? AND status = 'PENDING'";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $program_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'You already have a pending application for this program']);
        exit();
    }

    // Insert new application
    $sql = "INSERT INTO tbl_livelihood_applications 
            (user_id, livelihood_id, experience_level, current_situation, willing_training, reason, status, date_applied) 
            VALUES (?, ?, ?, ?, ?, ?, 'PENDING', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissss", $user_id, $program_id, $experience, $current_situation, $willing_training, $reason);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Application submitted successfully']);
    } else {
        throw new Exception("Error submitting application");
    }
} catch (Exception $e) {
    error_log("Error in process_livelihood.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error processing your application. Please try again.']);
}
?> 