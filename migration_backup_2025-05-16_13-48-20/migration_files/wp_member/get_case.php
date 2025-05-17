<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once "../wp_admin/includes/conn.php";

// Check if record ID is provided
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Record ID is required']);
    exit;
}

$record_id = (int)$_GET['id'];

try {
    // Get case details
    $stmt = $conn->prepare("SELECT * FROM tbl_household_case_records WHERE id = ? AND submitted_by = ?");
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
            <label class="fw-bold">Beneficiary Name:</label>
            <p>' . htmlspecialchars($record['beneficiary_lastname'] . ', ' . 
                $record['beneficiary_firstname'] . ' ' . 
                ($record['beneficiary_middlename'] ? $record['beneficiary_middlename'] : '')) . '</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Relationship:</label>
            <p>' . htmlspecialchars($record['beneficiary_relationship']) . '</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Age:</label>
            <p>' . htmlspecialchars($record['age']) . '</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Birth Date:</label>
            <p>' . date('F d, Y', strtotime($record['birth_date'])) . '</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Marital Status:</label>
            <p>' . htmlspecialchars($record['marital_status']) . '</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Educational Attainment:</label>
            <p>' . htmlspecialchars($record['educational_attainment']) . '</p>
        </div>
    </div>
    <div class="mb-3">
        <label class="fw-bold">Occupation:</label>
        <p>' . htmlspecialchars($record['occupation']) . '</p>
    </div>
    <div class="mb-3">
        <label class="fw-bold">Complete Address:</label>
        <p>' . htmlspecialchars($record['complete_address'] . ', ' . 
            $record['sitio_purok'] . ', ' . 
            $record['barangay'] . ', ' . 
            $record['town'] . ', ' . 
            $record['province']) . '</p>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Contact Number:</label>
            <p>' . ($record['contact_number'] ? htmlspecialchars($record['contact_number']) : 'Not provided') . '</p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Monthly Income:</label>
            <p>₱' . number_format($record['estimated_monthly_income'], 2) . '</p>
        </div>
    </div>
    <div class="mb-3">
        <label class="fw-bold">Problem Presented:</label>
        <p>' . nl2br(htmlspecialchars($record['problem_presented'])) . '</p>
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Status:</label>
            <p><span class="badge badge-' . 
                ($record['status'] === 'PENDING' ? 'warning' : 
                ($record['status'] === 'APPROVED' ? 'success' : 'danger')) . 
                '">' . htmlspecialchars($record['status']) . '</span></p>
        </div>
        <div class="col-md-6 mb-3">
            <label class="fw-bold">Date Submitted:</label>
            <p>' . date('F d, Y h:i A', strtotime($record['date_created'])) . '</p>
        </div>
    </div>';
    
    if ($record['remarks']) {
        $html .= '
        <div class="mb-3">
            <label class="fw-bold">Remarks:</label>
            <p>' . nl2br(htmlspecialchars($record['remarks'])) . '</p>
        </div>';
    }
    
    echo json_encode([
        'success' => true,
        'html' => $html
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_case.php: " . $e->getMessage());
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