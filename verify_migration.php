<?php
class MigrationVerifier {
    private $errors = [];
    private $warnings = [];
    private $success = [];
    private $logFile;
    private $backupDir;

    public function __construct() {
        $this->backupDir = __DIR__ . '/migration_backup_' . date('Y-m-d_H-i-s');
        $this->logFile = $this->backupDir . '/verification.log';
        
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function log($message, $type = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp][$type] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo $logMessage;
    }

    public function verifyDatabaseConnection() {
        $this->log("Verifying database connection...");
        
        try {
            require_once 'config/database.php';
            
            if ($conn->connect_error) {
                throw new Exception("Database connection failed: " . $conn->connect_error);
            }
            
            // Test database operations
            $testQuery = "SELECT 1";
            if (!$conn->query($testQuery)) {
                throw new Exception("Database query test failed");
            }
            
            $this->success[] = "Database connection verified successfully";
            $this->log("Database connection test passed", "SUCCESS");
        } catch (Exception $e) {
            $this->errors[] = "Database verification failed: " . $e->getMessage();
            $this->log("Database verification failed: " . $e->getMessage(), "ERROR");
        }
    }

    public function verifyFileStructure() {
        $this->log("Verifying file structure...");
        
        $requiredDirs = [
            'wp_admin',
            'wp_member',
            'config',
            'dist',
            'includes',
            'uploads',
            'videos',
            'assets'
        ];
        
        $requiredFiles = [
            'index.php',
            'header.php',
            'navbar.php',
            'scripts.php',
            'config/database.php'
        ];
        
        foreach ($requiredDirs as $dir) {
            if (!file_exists($dir)) {
                $this->errors[] = "Required directory missing: $dir";
                $this->log("Required directory missing: $dir", "ERROR");
            } else {
                $this->success[] = "Directory verified: $dir";
            }
        }
        
        foreach ($requiredFiles as $file) {
            if (!file_exists($file)) {
                $this->errors[] = "Required file missing: $file";
                $this->log("Required file missing: $file", "ERROR");
            } else {
                $this->success[] = "File verified: $file";
            }
        }
    }

    public function verifyVideoOptimization() {
        $this->log("Verifying video optimization...");
        
        $videoDir = __DIR__ . '/videos';
        $videos = glob($videoDir . '/*.mp4');
        
        foreach ($videos as $video) {
            $filename = basename($video);
            $fileSize = filesize($video);
            
            // Check if video is too large (over 100MB)
            if ($fileSize > 100 * 1024 * 1024) {
                $this->warnings[] = "Video file too large: $filename (" . round($fileSize / 1024 / 1024, 2) . "MB)";
                $this->log("Video file too large: $filename", "WARNING");
            }
            
            // Check video format and codec
            $command = sprintf('ffprobe -v error -select_streams v:0 -show_entries stream=codec_name -of default=noprint_wrappers=1:nokey=1 %s', escapeshellarg($video));
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0 && !empty($output)) {
                $codec = trim($output[0]);
                if ($codec !== 'h264') {
                    $this->warnings[] = "Video $filename is not using H.264 codec";
                    $this->log("Video codec warning: $filename", "WARNING");
                }
            }
        }
    }

    public function verifyIncludes() {
        $this->log("Verifying PHP includes...");
        
        $files = glob('*.php');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Check for common include patterns
            if (preg_match('/include\s*\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
                $includeFile = $matches[1];
                if (!file_exists($includeFile)) {
                    $this->errors[] = "Missing include file in $file: $includeFile";
                    $this->log("Missing include file in $file: $includeFile", "ERROR");
                }
            }
        }
    }

    public function verifyPermissions() {
        $this->log("Verifying file permissions...");
        
        $directories = [
            'uploads' => '777',
            'videos' => '755',
            'config' => '755'
        ];
        
        foreach ($directories as $dir => $requiredPerms) {
            if (file_exists($dir)) {
                $perms = substr(sprintf('%o', fileperms($dir)), -4);
                if ($perms !== $requiredPerms) {
                    $this->warnings[] = "Incorrect permissions on $dir: $perms (should be $requiredPerms)";
                    $this->log("Permission warning: $dir", "WARNING");
                }
            }
        }
    }

    public function optimizePHPFiles() {
        $this->log("Optimizing PHP files...");
        
        $files = glob('*.php');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Remove trailing whitespace
            $content = preg_replace('/[ \t]+$/', '', $content);
            
            // Remove empty lines at end of file
            $content = rtrim($content, "\n");
            
            // Add newline at end of file
            $content .= "\n";
            
            file_put_contents($file, $content);
            $this->success[] = "Optimized file: $file";
        }
    }

    public function generateReport() {
        $report = "Migration Verification Report\n";
        $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        if (!empty($this->success)) {
            $report .= "Success:\n";
            foreach ($this->success as $success) {
                $report .= "- $success\n";
            }
            $report .= "\n";
        }
        
        if (!empty($this->warnings)) {
            $report .= "Warnings:\n";
            foreach ($this->warnings as $warning) {
                $report .= "- $warning\n";
            }
            $report .= "\n";
        }
        
        if (!empty($this->errors)) {
            $report .= "Errors:\n";
            foreach ($this->errors as $error) {
                $report .= "- $error\n";
            }
            $report .= "\n";
        }
        
        file_put_contents($this->backupDir . '/verification_report.txt', $report);
        $this->log("Verification report generated", "INFO");
    }

    public function run() {
        $this->log("Starting migration verification...");
        
        $this->verifyDatabaseConnection();
        $this->verifyFileStructure();
        $this->verifyVideoOptimization();
        $this->verifyIncludes();
        $this->verifyPermissions();
        $this->optimizePHPFiles();
        $this->generateReport();
        
        if (empty($this->errors)) {
            $this->log("Verification completed successfully!", "SUCCESS");
        } else {
            $this->log("Verification completed with errors", "ERROR");
        }
        
        $this->log("Please check the verification report at: " . $this->backupDir . '/verification_report.txt');
    }
}

// Run the verification
$verifier = new MigrationVerifier();
$verifier->run();
?> 