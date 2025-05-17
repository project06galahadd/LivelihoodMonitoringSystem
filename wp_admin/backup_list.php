<?php
include 'includes/session.php';
include 'includes/conn.php';

header('Content-Type: application/json');

try {
    // Get all backups ordered by creation date
    $stmt = $conn->prepare("SELECT id, backup_type, backup_path, backup_size, created_at FROM tbl_backups ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $backups = array();
    while ($row = $result->fetch_assoc()) {
        $backups[] = array(
            'id' => $row['id'],
            'type' => $row['backup_type'],
            'path' => $row['backup_path'],
            'size' => $row['backup_size'] . ' MB',
            'date' => date('Y-m-d H:i:s', strtotime($row['created_at']))
        );
    }

    echo json_encode([
        'success' => true,
        'backups' => $backups
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error listing backups: ' . $e->getMessage()
    ]);
} 