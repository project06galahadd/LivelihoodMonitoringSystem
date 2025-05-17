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
    // Get case details with user information
    $sql = "SELECT h.*,
            CONCAT(u.FIRSTNAME, ' ', u.LASTNAME) as submitted_by_name,
            CONCAT(u2.FIRSTNAME, ' ', u2.LASTNAME) as approved_by_name,
            DATE_FORMAT(h.date_created, '%Y-%m-%d %H:%i:%s') as date_created,
            DATE_FORMAT(h.date_approved, '%Y-%m-%d %H:%i:%s') as date_approved,
            DATE_FORMAT(h.date_updated, '%Y-%m-%d %H:%i:%s') as date_updated
            FROM tbl_household_case_records h
            LEFT JOIN tbl_users u ON h.submitted_by = u.ID
            LEFT JOIN tbl_users u2 ON h.approved_by = u2.ID
            WHERE h.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $case_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Case record not found");
    }

    $case_data = $result->fetch_assoc();

    // Generate case number if not exists
    if (empty($case_data['case_number'])) {
        $year = date('Y');
        $month = date('m');
        $count_sql = "SELECT COUNT(*) as count FROM tbl_household_case_records WHERE YEAR(date_created) = ? AND MONTH(date_created) = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("ss", $year, $month);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result()->fetch_assoc();
        $count = $count_result['count'] + 1;
        $case_number = sprintf("HCR-%s%s-%04d", $year, $month, $count);
        
        // Update case number
        $update_sql = "UPDATE tbl_household_case_records SET case_number = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $case_number, $case_id);
        $update_stmt->execute();
        $case_data['case_number'] = $case_number;
    }

    echo json_encode(['success' => true, 'data' => $case_data]);

} catch (Exception $e) {
    error_log("Error in household_case_records_view.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?> 