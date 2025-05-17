<?php
class CodeQualityChecker {
    private $errors = [];
    private $warnings = [];
    private $suggestions = [];
    private $logFile;
    private $backupDir;

    public function __construct() {
        $this->backupDir = __DIR__ . '/migration_backup_' . date('Y-m-d_H-i-s');
        $this->logFile = $this->backupDir . '/code_quality.log';
        
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

    public function checkSecurity() {
        $this->log("Checking security practices...");
        
        $files = glob('*.php');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Check for SQL injection vulnerabilities
            if (preg_match('/\$_(GET|POST|REQUEST)\s*\[[^\]]+\]\s*\)/', $content)) {
                $this->warnings[] = "Potential SQL injection vulnerability in $file";
                $this->log("Security warning: Potential SQL injection in $file", "WARNING");
            }
            
            // Check for XSS vulnerabilities
            if (preg_match('/echo\s+\$_(GET|POST|REQUEST)\s*\[[^\]]+\]/', $content)) {
                $this->warnings[] = "Potential XSS vulnerability in $file";
                $this->log("Security warning: Potential XSS in $file", "WARNING");
            }
            
            // Check for proper session security
            if (strpos($content, 'session_start()') !== false && 
                strpos($content, 'session_regenerate_id') === false) {
                $this->suggestions[] = "Consider adding session_regenerate_id() in $file";
            }
        }
    }

    public function checkCodeStyle() {
        $this->log("Checking code style...");
        
        $files = glob('*.php');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Check for proper indentation
            if (preg_match('/^\t{2,}/m', $content)) {
                $this->suggestions[] = "Consider using spaces instead of tabs in $file";
            }
            
            // Check for line length
            $lines = explode("\n", $content);
            foreach ($lines as $lineNum => $line) {
                if (strlen($line) > 120) {
                    $this->suggestions[] = "Line $lineNum in $file exceeds 120 characters";
                }
            }
            
            // Check for proper spacing
            if (preg_match('/if\s*\([^)]+\)\s*{/', $content)) {
                $this->suggestions[] = "Consider adding space after if condition in $file";
            }
        }
    }

    public function checkDatabaseQueries() {
        $this->log("Checking database queries...");
        
        $files = glob('*.php');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Check for prepared statements
            if (preg_match('/\$conn->query\s*\([^)]+\)/', $content) && 
                !preg_match('/prepare\s*\(/', $content)) {
                $this->warnings[] = "Consider using prepared statements in $file";
                $this->log("Database warning: Missing prepared statements in $file", "WARNING");
            }
            
            // Check for proper error handling
            if (preg_match('/\$conn->query/', $content) && 
                !preg_match('/if\s*\(\$result\)/', $content)) {
                $this->suggestions[] = "Add error handling for database queries in $file";
            }
        }
    }

    public function checkPerformance() {
        $this->log("Checking performance...");
        
        $files = glob('*.php');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Check for loop optimization
            if (preg_match('/for\s*\([^;]+;[^;]+;\s*\$i\+\+\)/', $content)) {
                $this->suggestions[] = "Consider using foreach instead of for loop in $file";
            }
            
            // Check for proper array usage
            if (preg_match('/array_push\s*\(/', $content)) {
                $this->suggestions[] = "Consider using [] instead of array_push in $file";
            }
        }
    }

    public function checkDocumentation() {
        $this->log("Checking documentation...");
        
        $files = glob('*.php');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Check for function documentation
            if (preg_match('/function\s+\w+\s*\(/', $content) && 
                !preg_match('/\/\*\*[\s\S]*?\*\//', $content)) {
                $this->suggestions[] = "Add PHPDoc comments for functions in $file";
            }
            
            // Check for class documentation
            if (preg_match('/class\s+\w+/', $content) && 
                !preg_match('/\/\*\*[\s\S]*?\*\//', $content)) {
                $this->suggestions[] = "Add PHPDoc comments for classes in $file";
            }
        }
    }

    public function optimizeCode() {
        $this->log("Optimizing code...");
        
        $files = glob('*.php');
        foreach ($files as $file) {
            $content = file_get_contents($file);
            
            // Remove trailing whitespace
            $content = preg_replace('/[ \t]+$/', '', $content);
            
            // Fix line endings
            $content = str_replace("\r\n", "\n", $content);
            
            // Add proper spacing
            $content = preg_replace('/if\s*\(/', 'if (', $content);
            $content = preg_replace('/for\s*\(/', 'for (', $content);
            $content = preg_replace('/while\s*\(/', 'while (', $content);
            
            // Fix indentation
            $content = preg_replace('/^\t+/m', '    ', $content);
            
            file_put_contents($file, $content);
            $this->log("Optimized file: $file", "SUCCESS");
        }
    }

    public function generateReport() {
        $report = "Code Quality Report\n";
        $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        if (!empty($this->warnings)) {
            $report .= "Security and Performance Warnings:\n";
            foreach ($this->warnings as $warning) {
                $report .= "- $warning\n";
            }
            $report .= "\n";
        }
        
        if (!empty($this->suggestions)) {
            $report .= "Code Improvement Suggestions:\n";
            foreach ($this->suggestions as $suggestion) {
                $report .= "- $suggestion\n";
            }
            $report .= "\n";
        }
        
        file_put_contents($this->backupDir . '/code_quality_report.txt', $report);
        $this->log("Code quality report generated", "INFO");
    }

    public function run() {
        $this->log("Starting code quality check...");
        
        $this->checkSecurity();
        $this->checkCodeStyle();
        $this->checkDatabaseQueries();
        $this->checkPerformance();
        $this->checkDocumentation();
        $this->optimizeCode();
        $this->generateReport();
        
        $this->log("Code quality check completed", "INFO");
        $this->log("Please check the code quality report at: " . $this->backupDir . '/code_quality_report.txt');
    }
}

// Run the code quality checker
$checker = new CodeQualityChecker();
$checker->run();
?> 