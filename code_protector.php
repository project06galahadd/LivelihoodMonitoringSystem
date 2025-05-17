<?php
class CodeProtector {
    private $protectedFiles = [];
    private $logFile;
    private $backupDir;
    private $encryptionKey;
    private $allowedIPs = [];
    private $sessionTimeout = 3600; // 1 hour

    public function __construct() {
        $this->backupDir = __DIR__ . '/protection_backup_' . date('Y-m-d_H-i-s');
        $this->logFile = $this->backupDir . '/protection.log';
        $this->encryptionKey = $this->generateEncryptionKey();
        
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    private function generateEncryptionKey() {
        return bin2hex(random_bytes(32));
    }

    public function log($message, $type = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp][$type] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo $logMessage;
    }

    public function backupFile($file) {
        $backupPath = $this->backupDir . '/backups/' . dirname($file);
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
        copy($file, $backupPath . '/' . basename($file));
    }

    public function addIPProtection($file) {
        $this->log("Adding IP protection to $file");
        $content = file_get_contents($file);
        
        $ipProtection = <<<'EOT'
<?php
// IP Protection
$allowedIPs = ['127.0.0.1']; // Add your allowed IPs here
if (!in_array($_SERVER['REMOTE_ADDR'], $allowedIPs)) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access Denied');
}

EOT;
        
        // Add IP protection at the beginning of the file
        if (strpos($content, '<?php') !== false) {
            $content = str_replace('<?php', $ipProtection, $content);
        } else {
            $content = $ipProtection . $content;
        }
        
        file_put_contents($file, $content);
        $this->protectedFiles[] = $file;
    }

    public function addSessionProtection($file) {
        $this->log("Adding session protection to $file");
        $content = file_get_contents($file);
        
        $sessionProtection = <<<'EOT'
// Session Protection
session_start();
if (!isset($_SESSION['last_activity']) || (time() - $_SESSION['last_activity'] > 3600)) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
$_SESSION['last_activity'] = time();

EOT;
        
        // Add session protection after IP protection
        if (strpos($content, '// IP Protection') !== false) {
            $content = str_replace('// IP Protection', "// IP Protection\n" . $sessionProtection, $content);
        } else {
            $content = $sessionProtection . $content;
        }
        
        file_put_contents($file, $content);
        $this->protectedFiles[] = $file;
    }

    public function addInputValidation($file) {
        $this->log("Adding input validation to $file");
        $content = file_get_contents($file);
        
        $inputValidation = <<<'EOT'
// Input Validation
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate all input
$_GET = array_map('sanitizeInput', $_GET);
$_POST = array_map('sanitizeInput', $_POST);
$_REQUEST = array_map('sanitizeInput', $_REQUEST);

EOT;
        
        // Add input validation after session protection
        if (strpos($content, '$_SESSION[\'last_activity\']') !== false) {
            $content = str_replace('$_SESSION[\'last_activity\']', "// Input Validation\n" . $inputValidation . '$_SESSION[\'last_activity\']', $content);
        } else {
            $content = $inputValidation . $content;
        }
        
        file_put_contents($file, $content);
        $this->protectedFiles[] = $file;
    }

    public function addCSRFProtection($file) {
        $this->log("Adding CSRF protection to $file");
        $content = file_get_contents($file);
        
        $csrfProtection = <<<'EOT'
// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('HTTP/1.0 403 Forbidden');
        exit('CSRF token validation failed');
    }
}
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

EOT;
        
        // Add CSRF protection after input validation
        if (strpos($content, '$_REQUEST = array_map') !== false) {
            $content = str_replace('$_REQUEST = array_map', "// CSRF Protection\n" . $csrfProtection . '$_REQUEST = array_map', $content);
        } else {
            $content = $csrfProtection . $content;
        }
        
        file_put_contents($file, $content);
        $this->protectedFiles[] = $file;
    }

    public function addFileIntegrityCheck($file) {
        $this->log("Adding file integrity check to $file");
        $content = file_get_contents($file);
        
        $fileHash = hash_file('sha256', $file);
        $integrityCheck = <<<EOT
// File Integrity Check
if (hash_file('sha256', __FILE__) !== '$fileHash') {
    header('HTTP/1.0 403 Forbidden');
    exit('File integrity check failed');
}

EOT;
        
        // Add integrity check at the beginning of the file
        if (strpos($content, '<?php') !== false) {
            $content = str_replace('<?php', "<?php\n" . $integrityCheck, $content);
        } else {
            $content = $integrityCheck . $content;
        }
        
        file_put_contents($file, $content);
        $this->protectedFiles[] = $file;
    }

    public function addErrorHandling($file) {
        $this->log("Adding error handling to $file");
        $content = file_get_contents($file);
        
        $errorHandling = <<<'EOT'
// Error Handling
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno] $errstr on line $errline in file $errfile");
    return true;
});

// Exception Handling
set_exception_handler(function($exception) {
    error_log("Uncaught Exception: " . $exception->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    exit('An error occurred. Please try again later.');
});

EOT;
        
        // Add error handling after integrity check
        if (strpos($content, 'File integrity check') !== false) {
            $content = str_replace('File integrity check', "// Error Handling\n" . $errorHandling . 'File integrity check', $content);
        } else {
            $content = $errorHandling . $content;
        }
        
        file_put_contents($file, $content);
        $this->protectedFiles[] = $file;
    }

    public function addDatabaseProtection($file) {
        $this->log("Adding database protection to $file");
        $content = file_get_contents($file);
        
        $dbProtection = <<<'EOT'
// Database Protection
function secureQuery($conn, $query, $params = []) {
    try {
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            throw new Exception("Query preparation failed");
        }
        
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        
        return $stmt;
    } catch (Exception $e) {
        error_log("Database Error: " . $e->getMessage());
        throw new Exception("Database operation failed");
    }
}

EOT;
        
        // Add database protection if database operations are present
        if (strpos($content, '$conn') !== false || strpos($content, 'mysqli_') !== false) {
            if (strpos($content, 'function secureQuery') === false) {
                $content = $dbProtection . $content;
            }
        }
        
        file_put_contents($file, $content);
        $this->protectedFiles[] = $file;
    }

    public function generateReport() {
        $report = "Code Protection Report\n";
        $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        if (!empty($this->protectedFiles)) {
            $report .= "Protected Files:\n";
            foreach ($this->protectedFiles as $file) {
                $report .= "- $file\n";
            }
        } else {
            $report .= "No files were protected.\n";
        }
        
        file_put_contents($this->backupDir . '/protection_report.txt', $report);
        $this->log("Protection report generated", "INFO");
    }

    public function run() {
        $this->log("Starting code protection process...");
        
        $files = glob('*.php');
        foreach ($files as $file) {
            $this->backupFile($file);
            
            $this->addIPProtection($file);
            $this->addSessionProtection($file);
            $this->addInputValidation($file);
            $this->addCSRFProtection($file);
            $this->addFileIntegrityCheck($file);
            $this->addErrorHandling($file);
            $this->addDatabaseProtection($file);
        }
        
        $this->generateReport();
        
        $this->log("Code protection process completed", "INFO");
        $this->log("Please check the protection report at: " . $this->backupDir . '/protection_report.txt');
    }
}

// Run the code protection script
$protector = new CodeProtector();
$protector->run();
?> 