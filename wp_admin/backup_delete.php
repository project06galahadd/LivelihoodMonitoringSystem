<?php
include 'includes/session.php';
include 'includes/conn.php';

header('Content-Type: application/json');

try {
    // Get backup ID
    $id = $_POST['id'] ?? 0;
    
    // Get backup details
    $stmt = $conn->prepare("SELECT backup_path FROM tbl_backups WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($backup = $result->fetch_assoc()) {
        $backup_dir = '../backups';
        $file_path = "{$backup_dir}/{$backup['backup_path']}";
        
        // Delete file if it exists
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Delete database record
        $stmt = $conn->prepare("DELETE FROM tbl_backups WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        echo json_encode([
            'success' => true,
            'message' => 'Backup deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Backup not found'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error deleting backup: ' . $e->getMessage()
    ]);
} 