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
    // Get case history with user information
    $sql = "SELECT h.*, 
            CONCAT(u.FIRSTNAME, ' ', u.LASTNAME) as user_name,
            DATE_FORMAT(h.date_created, '%M %d, %Y') as formatted_date,
            DATE_FORMAT(h.date_created, '%h:%i %p') as formatted_time
            FROM tbl_household_case_history h
            LEFT JOIN tbl_users u ON h.user_id = u.ID
            WHERE h.case_id = ?
            ORDER BY h.date_created DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $case_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $history = [];
    while ($row = $result->fetch_assoc()) {
        $details = json_decode($row['details'], true);
        $history[] = [
            'date' => $row['formatted_date'],
            'time' => $row['formatted_time'],
            'action' => $row['action'],
            'details' => formatHistoryDetails($details, $row['action']),
            'user' => $row['user_name']
        ];
    }

    echo json_encode(['success' => true, 'history' => $history]);

} catch (Exception $e) {
    error_log("Error in household_case_records_history.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();

function formatHistoryDetails($details, $action) {
    $html = '';
    
    switch ($action) {
        case 'CREATE':
            $html = '<p>Case record created</p>';
            break;
            
        case 'UPDATE':
            $html = '<div class="changes">';
            if (isset($details['old']) && isset($details['new'])) {
                foreach ($details['new'] as $field => $new_value) {
                    $old_value = $details['old'][$field] ?? '';
                    if ($old_value !== $new_value) {
                        $html .= sprintf(
                            '<p><strong>%s:</strong> Changed from "%s" to "%s"</p>',
                            ucwords(str_replace('_', ' ', $field)),
                            htmlspecialchars($old_value),
                            htmlspecialchars($new_value)
                        );
                    }
                }
            }
            $html .= '</div>';
            break;
            
        case 'APPROVE':
            $html = sprintf(
                '<p>Case approved with remarks: "%s"</p>',
                htmlspecialchars($details['remarks'] ?? 'No remarks')
            );
            break;
            
        case 'REJECT':
            $html = sprintf(
                '<p>Case rejected with remarks: "%s"</p>',
                htmlspecialchars($details['remarks'] ?? 'No remarks')
            );
            break;
            
        default:
            $html = '<p>Action performed</p>';
    }
    
    return $html;
}
?> 