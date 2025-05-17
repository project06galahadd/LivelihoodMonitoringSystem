<?php
require_once 'includes/session.php';
require_once 'includes/conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$case_id = $_POST['case_id'] ?? null;
if (!$case_id) {
    echo json_encode(['success' => false, 'message' => 'Case ID is required']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Get current case data for history
    $current_sql = "SELECT * FROM tbl_household_case_records WHERE id = ?";
    $current_stmt = $conn->prepare($current_sql);
    $current_stmt->bind_param("i", $case_id);
    $current_stmt->execute();
    $current_data = $current_stmt->get_result()->fetch_assoc();

    // Update case record
    $update_sql = "UPDATE tbl_household_case_records SET 
        beneficiary_firstname = ?,
        beneficiary_lastname = ?,
        beneficiary_middlename = ?,
        contact_number = ?,
        estimated_monthly_income = ?,
        complete_address = ?,
        problem_presented = ?,
        remarks = ?,
        date_updated = NOW()
        WHERE id = ? AND status = 'APPROVED'";

    $update_stmt = $conn->prepare($update_sql);
    
    // Parse beneficiary name
    $name_parts = explode(',', $_POST['beneficiary_name']);
    $lastname = trim($name_parts[0]);
    $firstname_mi = isset($name_parts[1]) ? trim($name_parts[1]) : '';
    $firstname_mi_parts = explode(' ', $firstname_mi);
    $firstname = $firstname_mi_parts[0];
    $middlename = isset($firstname_mi_parts[1]) ? $firstname_mi_parts[1] : '';

    $update_stmt->bind_param(
        "ssssdsssi",
        $firstname,
        $lastname,
        $middlename,
        $_POST['contact_number'],
        $_POST['monthly_income'],
        $_POST['address'],
        $_POST['problem'],
        $_POST['remarks'],
        $case_id
    );

    if (!$update_stmt->execute()) {
        throw new Exception("Error updating case record: " . $update_stmt->error);
    }

    // Record history
    $history_sql = "INSERT INTO tbl_household_case_history 
        (case_id, action, details, user_id, date_created) 
        VALUES (?, 'UPDATE', ?, ?, NOW())";
    
    $history_details = json_encode([
        'old' => $current_data,
        'new' => [
            'beneficiary_name' => $_POST['beneficiary_name'],
            'contact_number' => $_POST['contact_number'],
            'monthly_income' => $_POST['monthly_income'],
            'address' => $_POST['address'],
            'problem' => $_POST['problem'],
            'remarks' => $_POST['remarks']
        ]
    ]);

    $history_stmt = $conn->prepare($history_sql);
    $history_stmt->bind_param("isi", $case_id, $history_details, $_SESSION['admin']);
    
    if (!$history_stmt->execute()) {
        throw new Exception("Error recording history: " . $history_stmt->error);
    }

    // Commit transaction
    $conn->commit();
    
    echo json_encode(['success' => true, 'message' => 'Case updated successfully']);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Error in household_case_records_update.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?> 