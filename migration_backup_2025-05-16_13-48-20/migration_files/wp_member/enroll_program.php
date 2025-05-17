<?php
session_start();
require_once '../includes/conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../signin.php');
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: available_programs.php');
    exit();
}

// Validate input
if (!isset($_POST['program_id']) || !is_numeric($_POST['program_id'])) {
    $_SESSION['error'] = "Invalid program selection";
    header('Location: available_programs.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$program_id = filter_var($_POST['program_id'], FILTER_SANITIZE_NUMBER_INT);

try {
    // Start transaction
    $conn->begin_transaction();

    // Check if program exists and is active
    $stmt = $conn->prepare("SELECT * FROM tbl_programs WHERE program_id = ? AND status = 'ACTIVE'");
    $stmt->bind_param("i", $program_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Program not found or not active");
    }
    
    $program = $result->fetch_assoc();
    
    // Check if user is already enrolled
    $stmt = $conn->prepare("SELECT * FROM tbl_enrolled_programs WHERE user_id = ? AND program_id = ?");
    $stmt->bind_param("ii", $user_id, $program_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception("You are already enrolled in this program");
    }
    
    // Check program capacity
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_enrolled_programs WHERE program_id = ? AND status IN ('PENDING', 'ACTIVE')");
    $stmt->bind_param("i", $program_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $enrolled_count = $result->fetch_assoc()['count'];
    
    if ($enrolled_count >= $program['capacity']) {
        throw new Exception("Program is already at full capacity");
    }
    
    // Insert enrollment
    $stmt = $conn->prepare("INSERT INTO tbl_enrolled_programs (user_id, program_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $program_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error enrolling in program: " . $stmt->error);
    }
    
    $enrollment_id = $stmt->insert_id;
    
    // Add to enrollment history
    $stmt = $conn->prepare("INSERT INTO tbl_enrollment_history (enrollment_id, status, updated_by) VALUES (?, 'PENDING', ?)");
    $stmt->bind_param("ii", $enrollment_id, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error recording enrollment history: " . $stmt->error);
    }
    
    // Commit transaction
    $conn->commit();
    
    $_SESSION['success'] = "Successfully enrolled in program. Your enrollment is pending approval.";
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error'] = $e->getMessage();
}

header('Location: available_programs.php');
exit(); 