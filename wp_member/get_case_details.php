<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if record ID is provided
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Record ID is required']);
    exit;
}

$record_id = (int)$_GET['id'];

try {
    // Get case details
    $stmt = $conn->prepare("SELECT * FROM tbl_household_case_records WHERE record_id = ? AND submitted_by = ?");
    if (!$stmt) {
        throw new Exception("Database prepare error: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $record_id, $_SESSION['user_id']);
    if (!$stmt->execute()) {
        throw new Exception("Database execute error: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Record not found']);
        exit;
    }
    
    $record = $result->fetch_assoc();
    
    // Generate HTML for the modal
    $html = '
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Household Head:</label>
            <p>' . htmlspecialchars($record['household_head']) . '</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Case Type:</label>
            <p>' . htmlspecialchars($record['case_type']) . '</p>
        </div>
    </div>
    <div class="mb-3">
        <label class="fw-bold">Address:</label>
        <p>' . htmlspecialchars($record['address']) . '</p>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Contact Number:</label>
            <p>' . ($record['contact_number'] ? htmlspecialchars($record['contact_number']) : 'Not provided') . '</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Family Size:</label>
            <p>' . htmlspecialchars($record['family_size']) . '</p>
        </div>
    </div>
    <div class="mb-3">
        <label class="fw-bold">Monthly Income:</label>
        <p>₱' . number_format($record['monthly_income'], 2) . '</p>
    </div>
    <div class="mb-3">
        <label class="fw-bold">Case Details:</label>
        <p>' . nl2br(htmlspecialchars($record['case_details'])) . '</p>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Status:</label>
            <p><span class="status-badge status-' . strtolower($record['status']) . '">' . htmlspecialchars($record['status']) . '</span></p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Date Submitted:</label>
            <p>' . date('F d, Y h:i A', strtotime($record['date_created'])) . '</p>
        </div>
    </div>';
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'record' => $record
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_case_details.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while retrieving the case details'
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 