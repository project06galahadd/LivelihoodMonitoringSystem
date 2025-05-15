<?php
session_start();
require_once 'includes/conn.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if (!isset($_POST['case_id'])) {
    echo json_encode(['success' => false, 'message' => 'No case ID provided']);
    exit();
}

if (!isset($_POST['remarks']) || empty($_POST['remarks'])) {
    echo json_encode(['success' => false, 'message' => 'Remarks are required for rejection']);
    exit();
}

$case_id = $_POST['case_id'];
    $remarks = $_POST['remarks'];

// Update case status
$sql = "UPDATE tbl_household_case_records SET 
        status = 'REJECTED',
        remarks = ?,
        approved_by = ?,
        date_approved = NOW()
        WHERE id = ? AND status = 'PENDING'";

    $stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $remarks, $_SESSION['user_id'], $case_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Case rejected successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Case not found or already processed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error rejecting case']);
} 