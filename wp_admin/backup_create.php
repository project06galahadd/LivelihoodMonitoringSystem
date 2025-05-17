<?php
include 'includes/session.php';
include 'includes/conn.php';
include 'backup_notify.php';

header('Content-Type: application/json');

try {
    // Get backup parameters
    $type = $_POST['type'] ?? 'full';
    $schedule = $_POST['schedule'] ?? 'manual';
    $retention = (int)($_POST['retention'] ?? 7);
    $compress = $_POST['compress'] ?? true;
    $include_uploads = $_POST['include_uploads'] ?? true;
    $include_config = $_POST['include_config'] ?? true;
    $include_plugins = $_POST['include_plugins'] ?? true;

    // Create backup directory if it doesn't exist
    $backup_dir = '../backups';
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }

    // Generate backup filename with timestamp
    $timestamp = date('Y-m-d_H-i-s');
    $backup_filename = "backup_{$timestamp}";
    
    if ($type === 'full') {
        // Full backup (Database + Files)
        
        // 1. Database backup
        $db_backup_file = "{$backup_dir}/{$backup_filename}_db.sql";
        $tables = array();
        $result = $conn->query("SHOW TABLES");
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }

        $db_backup = "";
        foreach ($tables as $table) {
            $result = $conn->query("SELECT * FROM $table");
            $numColumns = $result->field_count;
            
            $db_backup .= "DROP TABLE IF EXISTS $table;";
            
            $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_row();
            $db_backup .= "\n\n" . $row2[1] . ";\n\n";
            
            while ($row = $result->fetch_row()) {
                $db_backup .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $numColumns; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = str_replace("\n", "\\n", $row[$j]);
                    if (isset($row[$j])) {
                        $db_backup .= '"' . $row[$j] . '"';
                    } else {
                        $db_backup .= '""';
                    }
                    if ($j < ($numColumns - 1)) {
                        $db_backup .= ',';
                    }
                }
                $db_backup .= ");\n";
            }
            $db_backup .= "\n\n\n";
        }
        
        file_put_contents($db_backup_file, $db_backup);

        // 2. Files backup
        $files_backup = "{$backup_dir}/{$backup_filename}_files.zip";
        $zip = new ZipArchive();
        if ($zip->open($files_backup, ZipArchive::CREATE) === TRUE) {
            // Add uploads directory if enabled
            if ($include_uploads) {
                $uploads_dir = '../uploads';
                if (file_exists($uploads_dir)) {
                    $files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($uploads_dir),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );
                    foreach ($files as $file) {
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();
                            $relativePath = substr($filePath, strlen($uploads_dir) + 1);
                            $zip->addFile($filePath, 'uploads/' . $relativePath);
                        }
                    }
                }
            }

            // Add config files if enabled
            if ($include_config) {
                $config_files = ['../config.php', '../.htaccess'];
                foreach ($config_files as $config_file) {
                    if (file_exists($config_file)) {
                        $zip->addFile($config_file, basename($config_file));
                    }
                }
            }

            // Add plugins if enabled
            if ($include_plugins) {
                $plugins_dir = '../plugins';
                if (file_exists($plugins_dir)) {
                    $files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($plugins_dir),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );
                    foreach ($files as $file) {
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();
                            $relativePath = substr($filePath, strlen($plugins_dir) + 1);
                            $zip->addFile($filePath, 'plugins/' . $relativePath);
                        }
                    }
                }
            }

            $zip->close();
        }

        // 3. Create final backup archive
        $final_backup = "{$backup_dir}/{$backup_filename}.zip";
        $zip = new ZipArchive();
        if ($zip->open($final_backup, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($db_backup_file, 'database.sql');
            if (file_exists($files_backup)) {
                $zip->addFile($files_backup, 'files.zip');
            }
            $zip->close();
            
            // Clean up temporary files
            unlink($db_backup_file);
            if (file_exists($files_backup)) {
                unlink($files_backup);
            }
        }

    } else {
        // Database only backup
        $backup_file = "{$backup_dir}/{$backup_filename}.sql";
        $tables = array();
        $result = $conn->query("SHOW TABLES");
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }

        $backup = "";
        foreach ($tables as $table) {
            $result = $conn->query("SELECT * FROM $table");
            $numColumns = $result->field_count;
            
            $backup .= "DROP TABLE IF EXISTS $table;";
            
            $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_row();
            $backup .= "\n\n" . $row2[1] . ";\n\n";
            
            while ($row = $result->fetch_row()) {
                $backup .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $numColumns; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = str_replace("\n", "\\n", $row[$j]);
                    if (isset($row[$j])) {
                        $backup .= '"' . $row[$j] . '"';
                    } else {
                        $backup .= '""';
                    }
                    if ($j < ($numColumns - 1)) {
                        $backup .= ',';
                    }
                }
                $backup .= ");\n";
            }
            $backup .= "\n\n\n";
        }
        
        file_put_contents($backup_file, $backup);
    }

    // Save backup record to database
    $backup_path = $type === 'full' ? "{$backup_filename}.zip" : "{$backup_filename}.sql";
    $backup_size = filesize("{$backup_dir}/{$backup_path}");
    $backup_size = round($backup_size / 1024 / 1024, 2); // Convert to MB

    $stmt = $conn->prepare("INSERT INTO tbl_backups (backup_type, backup_path, backup_size, schedule, retention_days, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssdsi", $type, $backup_path, $backup_size, $schedule, $retention);
    $stmt->execute();

    // Clean up old backups based on retention period
    $stmt = $conn->prepare("DELETE FROM tbl_backups WHERE created_at < DATE_SUB(NOW(), INTERVAL retention_days DAY)");
    $stmt->execute();

    // Delete old backup files
    $old_backups = $conn->query("SELECT backup_path FROM tbl_backups WHERE created_at < DATE_SUB(NOW(), INTERVAL retention_days DAY)");
    while ($backup = $old_backups->fetch_assoc()) {
        $file = "{$backup_dir}/{$backup['backup_path']}";
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // Send success notification
    sendBackupNotification(
        $type,
        'Created',
        [
            'Size' => $backup_size . ' MB',
            'Schedule' => $schedule,
            'Retention' => $retention . ' days'
        ]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Backup created successfully',
        'backup' => [
            'type' => $type,
            'path' => $backup_path,
            'size' => $backup_size,
            'date' => date('Y-m-d H:i:s')
        ]
    ]);

} catch (Exception $e) {
    // Send error notification
    sendBackupNotification(
        'Backup',
        'Failed',
        [
            'Error' => $e->getMessage(),
            'Type' => $type ?? 'Unknown'
        ]
    );

    echo json_encode([
        'success' => false,
        'message' => 'Error creating backup: ' . $e->getMessage()
    ]);
} 