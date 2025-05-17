<?php
function sendBackupNotification($type, $status, $details = []) {
    // Get admin email from settings
    global $conn;
    $stmt = $conn->prepare("SELECT value FROM tbl_settings WHERE setting_key = 'admin_email'");
    $stmt->execute();
    $result = $stmt->get_result();
    $admin_email = $result->fetch_assoc()['value'] ?? '';

    if (empty($admin_email)) {
        error_log("Admin email not configured for backup notifications");
        return false;
    }

    $subject = "Backup {$status}: {$type}";
    
    $message = "Backup Operation Details:\n\n";
    $message .= "Type: {$type}\n";
    $message .= "Status: {$status}\n";
    $message .= "Time: " . date('Y-m-d H:i:s') . "\n\n";
    
    if (!empty($details)) {
        $message .= "Additional Details:\n";
        foreach ($details as $key => $value) {
            $message .= "{$key}: {$value}\n";
        }
    }
    
    $message .= "\nThis is an automated message from your backup system.";
    
    $headers = "From: " . $admin_email . "\r\n";
    $headers .= "Reply-To: " . $admin_email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return mail($admin_email, $subject, $message, $headers);
} 