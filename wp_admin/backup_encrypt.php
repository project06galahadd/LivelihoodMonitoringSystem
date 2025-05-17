<?php
include 'includes/session.php';
include 'includes/conn.php';
include 'backup_notify.php';

class BackupEncryption {
    private $key;
    private $cipher = 'aes-256-gcm';
    
    public function __construct($key = null) {
        if ($key === null) {
            // Get encryption key from settings or generate new one
            global $conn;
            $stmt = $conn->prepare("SELECT value FROM tbl_settings WHERE setting_key = 'backup_encryption_key'");
            $stmt->execute();
            $result = $stmt->get_result();
            $this->key = $result->fetch_assoc()['value'] ?? $this->generateKey();
        } else {
            $this->key = $key;
        }
    }
    
    private function generateKey() {
        $key = bin2hex(random_bytes(32));
        // Save key to settings
        global $conn;
        $stmt = $conn->prepare("INSERT INTO tbl_settings (setting_key, value) VALUES ('backup_encryption_key', ?) ON DUPLICATE KEY UPDATE value = ?");
        $stmt->bind_param("ss", $key, $key);
        $stmt->execute();
        return $key;
    }
    
    public function encrypt($file_path) {
        if (!file_exists($file_path)) {
            throw new Exception('File not found');
        }
        
        $data = file_get_contents($file_path);
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        $tag = '';
        
        $encrypted = openssl_encrypt(
            $data,
            $this->cipher,
            hex2bin($this->key),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        
        if ($encrypted === false) {
            throw new Exception('Encryption failed');
        }
        
        // Combine IV, tag, and encrypted data
        $result = $iv . $tag . $encrypted;
        
        // Save encrypted file
        $encrypted_path = $file_path . '.enc';
        file_put_contents($encrypted_path, $result);
        
        // Remove original file
        unlink($file_path);
        
        return $encrypted_path;
    }
    
    public function decrypt($file_path) {
        if (!file_exists($file_path)) {
            throw new Exception('File not found');
        }
        
        $data = file_get_contents($file_path);
        
        // Extract IV and tag
        $iv_length = openssl_cipher_iv_length($this->cipher);
        $tag_length = 16; // GCM tag length
        
        $iv = substr($data, 0, $iv_length);
        $tag = substr($data, $iv_length, $tag_length);
        $encrypted = substr($data, $iv_length + $tag_length);
        
        $decrypted = openssl_decrypt(
            $encrypted,
            $this->cipher,
            hex2bin($this->key),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        
        if ($decrypted === false) {
            throw new Exception('Decryption failed');
        }
        
        // Save decrypted file
        $decrypted_path = substr($file_path, 0, -4); // Remove .enc extension
        file_put_contents($decrypted_path, $decrypted);
        
        return $decrypted_path;
    }
}

// Handle encryption/decryption requests
header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? '';
    $backup_id = $_POST['id'] ?? 0;
    
    // Get backup details
    $stmt = $conn->prepare("SELECT backup_path, backup_type FROM tbl_backups WHERE id = ?");
    $stmt->bind_param("i", $backup_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($backup = $result->fetch_assoc()) {
        $backup_dir = '../backups';
        $file_path = "{$backup_dir}/{$backup['backup_path']}";
        
        $encryption = new BackupEncryption();
        
        if ($action === 'encrypt') {
            $encrypted_path = $encryption->encrypt($file_path);
            $new_path = basename($encrypted_path);
            
            // Update database record
            $stmt = $conn->prepare("UPDATE tbl_backups SET backup_path = ?, is_encrypted = 1 WHERE id = ?");
            $stmt->bind_param("si", $new_path, $backup_id);
            $stmt->execute();
            
            sendBackupNotification(
                'Encryption',
                'Completed',
                [
                    'Backup ID' => $backup_id,
                    'File' => $new_path
                ]
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Backup encrypted successfully'
            ]);
        } elseif ($action === 'decrypt') {
            $decrypted_path = $encryption->decrypt($file_path);
            $new_path = basename($decrypted_path);
            
            // Update database record
            $stmt = $conn->prepare("UPDATE tbl_backups SET backup_path = ?, is_encrypted = 0 WHERE id = ?");
            $stmt->bind_param("si", $new_path, $backup_id);
            $stmt->execute();
            
            sendBackupNotification(
                'Decryption',
                'Completed',
                [
                    'Backup ID' => $backup_id,
                    'File' => $new_path
                ]
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Backup decrypted successfully'
            ]);
        } else {
            throw new Exception('Invalid action');
        }
    } else {
        throw new Exception('Backup not found');
    }
    
} catch (Exception $e) {
    sendBackupNotification(
        'Encryption/Decryption',
        'Failed',
        [
            'Error' => $e->getMessage(),
            'Backup ID' => $backup_id ?? 'Unknown'
        ]
    );
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} 