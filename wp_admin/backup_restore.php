<?php
include 'includes/session.php';
include 'includes/conn.php';
include 'backup_notify.php';

header('Content-Type: application/json');

try {
    // Get backup ID
    $id = $_POST['id'] ?? 0;
    
    // Get backup details
    $stmt = $conn->prepare("SELECT backup_path, backup_type FROM tbl_backups WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($backup = $result->fetch_assoc()) {
        $backup_dir = '../backups';
        $file_path = "{$backup_dir}/{$backup['backup_path']}";
        
        if (file_exists($file_path)) {
            // Start restoration process
            if ($backup['backup_type'] === 'full') {
                // Extract the backup
                $zip = new ZipArchive();
                if ($zip->open($file_path) === TRUE) {
                    // Extract to temporary directory
                    $temp_dir = sys_get_temp_dir() . '/backup_restore_' . time();
                    mkdir($temp_dir);
                    $zip->extractTo($temp_dir);
                    $zip->close();
                    
                    // Restore database
                    $db_file = $temp_dir . '/database.sql';
                    if (file_exists($db_file)) {
                        // Read and execute SQL file
                        $sql = file_get_contents($db_file);
                        $conn->multi_query($sql);
                        
                        // Wait for all queries to complete
                        while ($conn->more_results() && $conn->next_result());
                    }
                    
                    // Restore files
                    $files_zip = $temp_dir . '/files.zip';
                    if (file_exists($files_zip)) {
                        $zip = new ZipArchive();
                        if ($zip->open($files_zip) === TRUE) {
                            $zip->extractTo('../');
                            $zip->close();
                        }
                    }
                    
                    // Clean up temporary directory
                    array_map('unlink', glob("$temp_dir/*.*"));
                    rmdir($temp_dir);
                }
            } else {
                // Database only backup
                $sql = file_get_contents($file_path);
                $conn->multi_query($sql);
                
                // Wait for all queries to complete
                while ($conn->more_results() && $conn->next_result());
            }
            
            // Send notification
            sendBackupNotification(
                $backup['backup_type'],
                'Restored',
                [
                    'Backup ID' => $id,
                    'File' => $backup['backup_path']
                ]
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Backup restored successfully'
            ]);
        } else {
            throw new Exception('Backup file not found');
        }
    } else {
        throw new Exception('Backup not found');
    }
    
} catch (Exception $e) {
    // Send error notification
    sendBackupNotification(
        'Restore',
        'Failed',
        [
            'Error' => $e->getMessage(),
            'Backup ID' => $id ?? 'Unknown'
        ]
    );
    
    echo json_encode([
        'success' => false,
        'message' => 'Error restoring backup: ' . $e->getMessage()
    ]);
} 