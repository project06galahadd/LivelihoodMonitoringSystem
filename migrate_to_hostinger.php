<?php
// Migration Script for Hostinger Deployment
class HostingerMigration
{
    private $config;
    private $backupDir;
    private $logFile;
    private $errors = [];
    private $maxParallelProcesses = 4; // Number of parallel processes

    public function __construct()
    {
        $this->backupDir = __DIR__ . '/migration_backup_' . date('Y-m-d_H-i-s');
        $this->logFile = $this->backupDir . '/migration.log';

        // Create backup directory
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        echo $logMessage;
    }

    public function backupDatabase()
    {
        $this->log("Starting database backup...");

        try {
            require_once 'config/database.php';

            // Create backup filename with compression
            $backupFile = $this->backupDir . '/database_backup.sql.gz';

            // Optimized mysqldump command with compression
            $command = sprintf(
                'mysqldump -h %s -u %s -p%s %s --single-transaction --quick --lock-tables=false | gzip > %s',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($backupFile)
            );

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new Exception("Database backup failed");
            }

            $this->log("Database backup completed successfully");
            return true;
        } catch (Exception $e) {
            $this->errors[] = "Database backup failed: " . $e->getMessage();
            $this->log("ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function optimizeVideos()
    {
        $this->log("Starting video optimization...");

        $videoDir = __DIR__ . '/videos';
        $optimizedDir = $this->backupDir . '/optimized_videos';

        if (!file_exists($optimizedDir)) {
            mkdir($optimizedDir, 0755, true);
        }

        $videos = glob($videoDir . '/*.mp4');
        $processes = [];

        foreach ($videos as $video) {
            $filename = basename($video);
            $this->log("Queueing video for optimization: $filename");

            $outputFile = $optimizedDir . '/' . $filename;

            // Optimized FFmpeg command for faster processing
            $command = sprintf(
                'ffmpeg -i %s -vf "scale=1280:-1" -c:v libx264 -crf 23 -preset ultrafast -c:a aac -b:a 128k -threads 0 %s',
                escapeshellarg($video),
                escapeshellarg($outputFile)
            );

            // Run in background
            $process = popen($command . ' > /dev/null 2>&1 &', 'r');
            $processes[] = $process;

            // Limit parallel processes
            if (count($processes) >= $this->maxParallelProcesses) {
                foreach ($processes as $p) {
                    pclose($p);
                }
                $processes = [];
            }
        }

        // Close remaining processes
        foreach ($processes as $p) {
            pclose($p);
        }
    }

    public function prepareFiles()
    {
        $this->log("Preparing files for migration...");

        $migrationDir = $this->backupDir . '/migration_files';
        if (!file_exists($migrationDir)) {
            mkdir($migrationDir, 0755, true);
        }

        $directories = [
            'wp_admin',
            'wp_member',
            'config',
            'dist',
            'includes',
            'uploads',
            'videos',
            'assets'
        ];

        // Parallel directory copying
        $processes = [];
        foreach ($directories as $dir) {
            if (file_exists($dir)) {
                $this->log("Queueing directory for copy: $dir");

                // Use rsync for faster copying
                $command = sprintf(
                    'rsync -av --delete %s/ %s/%s/',
                    escapeshellarg($dir),
                    escapeshellarg($migrationDir),
                    escapeshellarg($dir)
                );

                $process = popen($command . ' > /dev/null 2>&1 &', 'r');
                $processes[] = $process;

                if (count($processes) >= $this->maxParallelProcesses) {
                    foreach ($processes as $p) {
                        pclose($p);
                    }
                    $processes = [];
                }
            }
        }

        // Close remaining processes
        foreach ($processes as $p) {
            pclose($p);
        }

        // Copy PHP files
        $files = glob('*.php');
        foreach ($files as $file) {
            if ($file !== 'migrate_to_hostinger.php') {
                $this->log("Copying file: $file");
                copy($file, $migrationDir . '/' . $file);
            }
        }
    }

    public function updateConfigurations()
    {
        $this->log("Updating configurations for Hostinger...");

        // Update database configuration
        $dbConfigFile = $this->backupDir . '/migration_files/config/database.php';
        if (file_exists($dbConfigFile)) {
            $content = file_get_contents($dbConfigFile);

            // Replace database configuration
            $content = preg_replace(
                '/\$host\s*=\s*[\'"].*?[\'"];/',
                '$host = DB_HOST;',
                $content
            );
            $content = preg_replace(
                '/\$username\s*=\s*[\'"].*?[\'"];/',
                '$username = DB_USER;',
                $content
            );
            $content = preg_replace(
                '/\$password\s*=\s*[\'"].*?[\'"];/',
                '$password = DB_PASS;',
                $content
            );
            $content = preg_replace(
                '/\$database\s*=\s*[\'"].*?[\'"];/',
                '$database = DB_NAME;',
                $content
            );

            file_put_contents($dbConfigFile, $content);
        }
    }

    public function createDeploymentInstructions()
    {
        $instructions = <<<EOT
# Hostinger Deployment Instructions

## 1. Database Setup
1. Log in to your Hostinger control panel
2. Go to MySQL Databases
3. Create a new database
4. Create a new database user
5. Assign the user to the database with all privileges
6. Note down the database name, username, and password

## 2. File Upload
1. Log in to your Hostinger File Manager
2. Navigate to public_html
3. Upload all files from the migration_files directory
4. Set proper permissions:
   - Directories: 755
   - Files: 644
   - Upload directories: 777

## 3. Configuration
1. Update config/hostinger_config.php with your Hostinger credentials:
   - DB_HOST
   - DB_USER
   - DB_PASS
   - DB_NAME
   - BASE_URL
   - UPLOAD_PATH
   - VIDEO_PATH

## 4. Database Import
1. Go to phpMyAdmin in Hostinger
2. Select your database
3. Import the database_backup.sql file

## 5. Final Steps
1. Test the application
2. Check error logs
3. Verify all functionality
4. Remove migration files

## Troubleshooting
- Check error logs in /home/username/logs/php_errors.log
- Verify file permissions
- Ensure all required PHP extensions are enabled
- Check database connection settings

EOT;

        file_put_contents($this->backupDir . '/DEPLOYMENT_INSTRUCTIONS.md', $instructions);
        $this->log("Created deployment instructions");
    }

    public function createArchive()
    {
        $this->log("Creating deployment archive...");

        $archiveFile = $this->backupDir . '/deployment.zip';

        // Create ZIP archive with parallel compression
        $command = sprintf(
            'cd %s && zip -r -9 -q %s migration_files/ database_backup.sql.gz DEPLOYMENT_INSTRUCTIONS.md',
            escapeshellarg($this->backupDir),
            escapeshellarg($archiveFile)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->errors[] = "Failed to create deployment archive";
            $this->log("ERROR: Failed to create deployment archive");
        } else {
            $this->log("Deployment archive created successfully");
        }
    }

    public function run()
    {
        $this->log("Starting migration process...");

        // Run all migration steps
        $this->backupDatabase();
        $this->optimizeVideos();
        $this->prepareFiles();
        $this->updateConfigurations();
        $this->createDeploymentInstructions();
        $this->createArchive();

        if (empty($this->errors)) {
            $this->log("Migration completed successfully!");
            $this->log("Deployment package created at: " . $this->backupDir . '/deployment.zip');
        } else {
            $this->log("Migration completed with errors:");
            foreach ($this->errors as $error) {
                $this->log("ERROR: " . $error);
            }
        }
    }
}

// Run the migration
$migration = new HostingerMigration();
$migration->run();
