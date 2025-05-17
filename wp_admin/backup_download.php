<?php
include 'includes/session.php';
include 'includes/conn.php';

try {
    // Get backup ID
    $id = $_GET['id'] ?? 0;
    
    // Get backup details
    $stmt = $conn->prepare("SELECT backup_path, backup_type FROM tbl_backups WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($backup = $result->fetch_assoc()) {
        $backup_dir = '../backups';
        $file_path = "{$backup_dir}/{$backup['backup_path']}";
        
        if (file_exists($file_path)) {
            // Set headers for download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Content-Length: ' . filesize($file_path));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            // Output file
            readfile($file_path);
            exit;
        }
    }
    
    // If we get here, something went wrong
    $_SESSION['error'] = 'Backup file not found';
    header('Location: setting.php');
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = 'Error downloading backup: ' . $e->getMessage();
    header('Location: setting.php');
    exit;
} 