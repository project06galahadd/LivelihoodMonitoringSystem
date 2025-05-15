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

$case_id = $_POST['case_id'];
    
// Get case details
$sql = "SELECT h.*, 
        CONCAT(u1.firstname, ' ', u1.lastname) as submitted_by_name,
        CONCAT(u2.firstname, ' ', u2.lastname) as approved_by_name
            FROM tbl_household_case_records h 
        LEFT JOIN tbl_users u1 ON h.submitted_by = u1.user_id 
        LEFT JOIN tbl_users u2 ON h.approved_by = u2.user_id 
            WHERE h.id = ?";

    $stmt = $conn->prepare($sql);
$stmt->bind_param("i", $case_id);
    $stmt->execute();
    $result = $stmt->get_result();

if ($result->num_rows > 0) {
    $case_data = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $case_data]);
                } else {
    echo json_encode(['success' => false, 'message' => 'Case not found']);
} 