<?php
require_once 'includes/session.php';
require_once 'includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['admin'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

$case_id = $_POST['case_id'] ?? null;
$remarks = $_POST['remarks'] ?? '';

if (!$case_id) {
    echo json_encode(['success' => false, 'message' => 'Case ID is required']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Get current case data for history
    $current_sql = "SELECT * FROM tbl_household_case_records WHERE id = ? AND status = 'PENDING'";
    $current_stmt = $conn->prepare($current_sql);
    $current_stmt->bind_param("i", $case_id);
    $current_stmt->execute();
    $current_data = $current_stmt->get_result()->fetch_assoc();

    if (!$current_data) {
        throw new Exception("Case not found or not pending");
    }

    // Update case status
    $update_sql = "UPDATE tbl_household_case_records SET
        status = 'APPROVED',
        remarks = ?,
        approved_by = ?,
        date_approved = NOW(),
        date_updated = NOW()
        WHERE id = ? AND status = 'PENDING'";

    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sii", $remarks, $_SESSION['admin'], $case_id);

    if (!$update_stmt->execute()) {
        throw new Exception("Error approving case: " . $update_stmt->error);
    }

    // Record history
    $history_sql = "INSERT INTO tbl_household_case_history
        (case_id, action, details, user_id, date_created)
        VALUES (?, 'APPROVE', ?, ?, NOW())";
    $history_details = json_encode([
        'remarks' => $remarks,
        'previous_status' => 'PENDING',
        'new_status' => 'APPROVED'
    ]);

    $history_stmt = $conn->prepare($history_sql);
    $history_stmt->bind_param("isi", $case_id, $history_details, $_SESSION['admin']);
    if (!$history_stmt->execute()) {
        throw new Exception("Error recording history: " . $history_stmt->error);
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Case approved successfully']);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Error in household_case_records_approve.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
