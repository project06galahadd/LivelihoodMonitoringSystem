<?php
include 'includes/conn.php';
include 'backup_notify.php';

try {
    // Get all scheduled backups
    $stmt = $conn->prepare("SELECT id, backup_type, schedule, retention_days FROM tbl_backups WHERE schedule != 'manual'");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $backups_created = 0;
    $backups_failed = 0;
    
    while ($backup = $result->fetch_assoc()) {
        $should_backup = false;
        $last_backup = $conn->query("SELECT created_at FROM tbl_backups WHERE schedule = '{$backup['schedule']}' ORDER BY created_at DESC LIMIT 1")->fetch_assoc();
        
        if ($last_backup) {
            $last_backup_time = strtotime($last_backup['created_at']);
            $current_time = time();
            
            switch ($backup['schedule']) {
                case 'daily':
                    $should_backup = ($current_time - $last_backup_time) >= 86400; // 24 hours
                    break;
                case 'weekly':
                    $should_backup = ($current_time - $last_backup_time) >= 604800; // 7 days
                    break;
                case 'monthly':
                    $should_backup = ($current_time - $last_backup_time) >= 2592000; // 30 days
                    break;
            }
        } else {
            $should_backup = true;
        }
        
        if ($should_backup) {
            try {
                // Create new backup
                $_POST['type'] = $backup['backup_type'];
                $_POST['schedule'] = $backup['schedule'];
                $_POST['retention'] = $backup['retention_days'];
                
                include 'backup_create.php';
                $backups_created++;
            } catch (Exception $e) {
                $backups_failed++;
                error_log("Failed to create scheduled backup: " . $e->getMessage());
            }
        }
    }
    
    // Send summary notification
    if ($backups_created > 0 || $backups_failed > 0) {
        sendBackupNotification(
            'Scheduled',
            'Completed',
            [
                'Backups Created' => $backups_created,
                'Backups Failed' => $backups_failed,
                'Time' => date('Y-m-d H:i:s')
            ]
        );
    }
    
    echo "Scheduled backups completed successfully\n";
    echo "Created: {$backups_created}\n";
    echo "Failed: {$backups_failed}\n";
    
} catch (Exception $e) {
    // Send error notification
    sendBackupNotification(
        'Scheduled',
        'Failed',
        [
            'Error' => $e->getMessage()
        ]
    );
    
    echo "Error in scheduled backups: " . $e->getMessage() . "\n";
} 