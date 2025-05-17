<?php
class AutoFix {
    private $fixedFiles = [];
    private $logFile;
    private $backupDir;

    public function __construct() {
        $this->backupDir = __DIR__ . '/migration_backup_' . date('Y-m-d_H-i-s');
        $this->logFile = $this->backupDir . '/auto_fix.log';
        
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

    public function backupFile($file) {
        $backupPath = $this->backupDir . '/backups/' . dirname($file);
        if (!file_exists($backupPath)) {
            mkdir($backupPath, 0755, true);
        }
        copy($file, $backupPath . '/' . basename($file));
    }

    public function fixTrailingWhitespace($file) {
        $this->log("Fixing trailing whitespace in $file");
        $content = file_get_contents($file);
        
        // Remove trailing whitespace
        $content = preg_replace('/[ \t]+$/', '', $content);
        
        // Remove empty lines at end of file
        $content = rtrim($content, "\n");
        
        // Add single newline at end of file
        $content .= "\n";
        
        file_put_contents($file, $content);
        $this->fixedFiles[] = $file;
    }

    public function fixIndentation($file) {
        $this->log("Fixing indentation in $file");
        $content = file_get_contents($file);
        
        // Convert tabs to spaces
        $content = str_replace("\t", "    ", $content);
        
        // Fix mixed indentation
        $lines = explode("\n", $content);
        $fixedLines = [];
        $indentLevel = 0;
        
        foreach ($lines as $line) {
            // Count leading spaces
            $leadingSpaces = strlen($line) - strlen(ltrim($line));
            $indentLevel = floor($leadingSpaces / 4);
            
            // Create properly indented line
            $fixedLine = str_repeat("    ", $indentLevel) . ltrim($line);
            $fixedLines[] = $fixedLine;
        }
        
        $content = implode("\n", $fixedLines);
        file_put_contents($file, $content);
        $this->fixedFiles[] = $file;
    }

    public function fixLineEndings($file) {
        $this->log("Fixing line endings in $file");
        $content = file_get_contents($file);
        
        // Convert all line endings to Unix style
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        
        file_put_contents($file, $content);
        $this->fixedFiles[] = $file;
    }

    public function fixSpacing($file) {
        $this->log("Fixing spacing in $file");
        $content = file_get_contents($file);
        
        // Fix spacing around operators
        $content = preg_replace('/\s*([=!<>+\-*\/])\s*/', ' $1 ', $content);
        
        // Fix spacing in control structures
        $content = preg_replace('/if\s*\(/', 'if (', $content);
        $content = preg_replace('/for\s*\(/', 'for (', $content);
        $content = preg_replace('/while\s*\(/', 'while (', $content);
        $content = preg_replace('/foreach\s*\(/', 'foreach (', $content);
        
        // Fix spacing in function calls
        $content = preg_replace('/function\s+(\w+)\s*\(/', 'function $1(', $content);
        
        file_put_contents($file, $content);
        $this->fixedFiles[] = $file;
    }

    public function fixSecurityIssues($file) {
        $this->log("Fixing security issues in $file");
        $content = file_get_contents($file);
        
        // Add session security
        if (strpos($content, 'session_start()') !== false && 
            strpos($content, 'session_regenerate_id') === false) {
            $content = str_replace(
                'session_start();',
                "session_start();\nsession_regenerate_id(true);",
                $content
            );
        }
        
        // Add basic XSS protection
        $content = preg_replace(
            '/echo\s+\$_(GET|POST|REQUEST)\s*\[[^\]]+\]/',
            'echo htmlspecialchars($_$1[$2], ENT_QUOTES, \'UTF-8\')',
            $content
        );
        
        file_put_contents($file, $content);
        $this->fixedFiles[] = $file;
    }

    public function fixDatabaseQueries($file) {
        $this->log("Fixing database queries in $file");
        $content = file_get_contents($file);
        
        // Convert direct queries to prepared statements
        if (preg_match('/\$conn->query\s*\([^)]+\)/', $content)) {
            $content = preg_replace(
                '/\$conn->query\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/',
                '$stmt = $conn->prepare("$1"); $stmt->execute();',
                $content
            );
        }
        
        file_put_contents($file, $content);
        $this->fixedFiles[] = $file;
    }

    public function fixPerformanceIssues($file) {
        $this->log("Fixing performance issues in $file");
        $content = file_get_contents($file);
        
        // Convert array_push to []
        $content = preg_replace(
            '/array_push\s*\(\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*,\s*([^)]+)\s*\)/',
            '$$1[] = $2',
            $content
        );
        
        // Convert for loops to foreach where possible
        $content = preg_replace(
            '/for\s*\(\s*\$i\s*=\s*0\s*;\s*\$i\s*<\s*count\s*\(\s*\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\)\s*;\s*\$i\+\+\)/',
            'foreach ($$1 as $item)',
            $content
        );
        
        file_put_contents($file, $content);
        $this->fixedFiles[] = $file;
    }

    public function fixDocumentation($file) {
        $this->log("Adding documentation to $file");
        $content = file_get_contents($file);
        
        // Add class documentation
        if (preg_match('/class\s+(\w+)/', $content, $matches) && 
            !preg_match('/\/\*\*[\s\S]*?\*\//', $content)) {
            $className = $matches[1];
            $docBlock = "/**\n * Class $className\n *\n * @package LivelihoodMonitoringSystem\n */\n";
            $content = preg_replace('/class\s+' . $className . '/', $docBlock . 'class ' . $className, $content);
        }
        
        // Add function documentation
        if (preg_match('/function\s+(\w+)\s*\(/', $content, $matches) && 
            !preg_match('/\/\*\*[\s\S]*?\*\//', $content)) {
            $functionName = $matches[1];
            $docBlock = "/**\n * $functionName\n *\n * @return void\n */\n";
            $content = preg_replace('/function\s+' . $functionName . '/', $docBlock . 'function ' . $functionName, $content);
        }
        
        file_put_contents($file, $content);
        $this->fixedFiles[] = $file;
    }

    public function generateReport() {
        $report = "Auto-Fix Report\n";
        $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        if (!empty($this->fixedFiles)) {
            $report .= "Fixed Files:\n";
            foreach ($this->fixedFiles as $file) {
                $report .= "- $file\n";
            }
        } else {
            $report .= "No files were fixed.\n";
        }
        
        file_put_contents($this->backupDir . '/auto_fix_report.txt', $report);
        $this->log("Auto-fix report generated", "INFO");
    }

    public function run() {
        $this->log("Starting auto-fix process...");
        
        $files = glob('*.php');
        foreach ($files as $file) {
            $this->backupFile($file);
            
            $this->fixTrailingWhitespace($file);
            $this->fixIndentation($file);
            $this->fixLineEndings($file);
            $this->fixSpacing($file);
            $this->fixSecurityIssues($file);
            $this->fixDatabaseQueries($file);
            $this->fixPerformanceIssues($file);
            $this->fixDocumentation($file);
        }
        
        $this->generateReport();
        
        $this->log("Auto-fix process completed", "INFO");
        $this->log("Please check the auto-fix report at: " . $this->backupDir . '/auto_fix_report.txt');
    }
}

// Run the auto-fix script
$fixer = new AutoFix();
$fixer->run();
?> 