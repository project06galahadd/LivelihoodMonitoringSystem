<?php
include 'includes/session.php';
include 'includes/conn.php';
include 'backup_notify.php';

header('Content-Type: application/json');

function verifyBackup($backup_path, $backup_type) {
    $backup_dir = '../backups';
    $file_path = "{$backup_dir}/{$backup_path}";
    
    if (!file_exists($file_path)) {
        throw new Exception('Backup file not found');
    }
    
    $verification = [
        'file_exists' => true,
        'file_size' => filesize($file_path),
        'file_modified' => date('Y-m-d H:i:s', filemtime($file_path)),
        'file_hash' => hash_file('sha256', $file_path),
        'is_readable' => is_readable($file_path),
        'is_valid' => false,
        'details' => []
    ];
    
    if ($backup_type === 'full') {
        // Verify ZIP structure
        $zip = new ZipArchive();
        if ($zip->open($file_path) === TRUE) {
            $verification['details']['zip_status'] = 'valid';
            $verification['details']['file_count'] = $zip->numFiles;
            
            // Check for required files
            $required_files = ['database.sql', 'files.zip'];
            $missing_files = [];
            
            foreach ($required_files as $file) {
                if ($zip->locateName($file) === false) {
                    $missing_files[] = $file;
                }
            }
            
            if (empty($missing_files)) {
                // Verify database backup
                $temp_dir = sys_get_temp_dir() . '/backup_verify_' . time();
                mkdir($temp_dir);
                $zip->extractTo($temp_dir, 'database.sql');
                $zip->close();
                
                $db_file = $temp_dir . '/database.sql';
                if (file_exists($db_file)) {
                    $sql_content = file_get_contents($db_file);
                    $verification['details']['db_size'] = strlen($sql_content);
                    $verification['details']['db_tables'] = substr_count($sql_content, 'CREATE TABLE');
                    $verification['is_valid'] = true;
                }
                
                // Clean up
                unlink($db_file);
                rmdir($temp_dir);
            } else {
                $verification['details']['missing_files'] = $missing_files;
            }
        } else {
            $verification['details']['zip_status'] = 'invalid';
        }
    } else {
        // Verify SQL file
        $sql_content = file_get_contents($file_path);
        $verification['details']['db_size'] = strlen($sql_content);
        $verification['details']['db_tables'] = substr_count($sql_content, 'CREATE TABLE');
        $verification['is_valid'] = true;
    }
    
    return $verification;
}

try {
    // Get backup ID
    $id = $_POST['id'] ?? 0;
    
    // Get backup details
    $stmt = $conn->prepare("SELECT backup_path, backup_type FROM tbl_backups WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($backup = $result->fetch_assoc()) {
        $verification = verifyBackup($backup['backup_path'], $backup['backup_type']);
        
        // Update verification status in database
        $stmt = $conn->prepare("UPDATE tbl_backups SET verification_status = ?, verification_date = NOW(), verification_details = ? WHERE id = ?");
        $status = $verification['is_valid'] ? 'verified' : 'failed';
        $details = json_encode($verification);
        $stmt->bind_param("ssi", $status, $details, $id);
        $stmt->execute();
        
        // Send notification
        sendBackupNotification(
            'Verification',
            $status,
            [
                'Backup ID' => $id,
                'File' => $backup['backup_path'],
                'Status' => $status,
                'Details' => json_encode($verification['details'])
            ]
        );
        
        echo json_encode([
            'success' => true,
            'verification' => $verification
        ]);
    } else {
        throw new Exception('Backup not found');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error verifying backup: ' . $e->getMessage()
    ]);
} 