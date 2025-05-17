<?php
session_start();
require_once '../includes/conn.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    header('Location: ../signin.php');
    exit();
}

// Function to create database backup
function createDatabaseBackup($conn) {
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    $backup = "";
    foreach ($tables as $table) {
        $result = $conn->query("SHOW CREATE TABLE $table");
        $row = $result->fetch_row();
        $backup .= "\n\n" . $row[1] . ";\n\n";

        $result = $conn->query("SELECT * FROM $table");
        while ($row = $result->fetch_assoc()) {
            $backup .= "INSERT INTO $table VALUES('";
            $backup .= implode("','", array_map('addslashes', $row));
            $backup .= "');\n";
        }
    }

    $backup_file = '../backups/db_backup_' . date('Y-m-d_H-i-s') . '.sql';
    file_put_contents($backup_file, $backup);
    return basename($backup_file);
}

// Function to create file backup
function createFileBackup() {
    $source = '../uploads';
    $backup_dir = '../backups/files';
    $backup_name = 'file_backup_' . date('Y-m-d_H-i-s') . '.zip';
    $backup_path = $backup_dir . '/' . $backup_name;

    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }

    $zip = new ZipArchive();
    if ($zip->open($backup_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($source) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        return $backup_name;
    }
    return false;
}

// Handle backup requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['backup_type'])) {
        try {
            if ($_POST['backup_type'] === 'database') {
                $backup_file = createDatabaseBackup($conn);
                $_SESSION['success'] = "Database backup created successfully: $backup_file";
            } elseif ($_POST['backup_type'] === 'files') {
                $backup_file = createFileBackup();
                if ($backup_file) {
                    $_SESSION['success'] = "File backup created successfully: $backup_file";
                } else {
                    throw new Exception("Failed to create file backup");
                }
            } elseif ($_POST['backup_type'] === 'both') {
                $db_backup = createDatabaseBackup($conn);
                $file_backup = createFileBackup();
                if ($file_backup) {
                    $_SESSION['success'] = "Full backup created successfully. Database: $db_backup, Files: $file_backup";
                } else {
                    throw new Exception("Failed to create file backup");
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Backup failed: " . $e->getMessage();
        }
        header('Location: backup.php');
        exit();
    }
}

// Get list of existing backups
$backups = [
    'database' => [],
    'files' => []
];

$backup_dir = '../backups';
if (file_exists($backup_dir)) {
    $files = scandir($backup_dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        if (strpos($file, 'db_backup_') === 0) {
            $backups['database'][] = $file;
        } elseif (strpos($file, 'file_backup_') === 0) {
            $backups['files'][] = $file;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Management - Livelihood Monitoring System</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <style>
        .backup-card {
            transition: transform 0.3s;
        }
        .backup-card:hover {
            transform: translateY(-5px);
        }
        .backup-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .backup-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s;
        }
        .backup-item:hover {
            background-color: #f8f9fa;
        }
        .backup-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include '../navbar.php'; ?>
        <?php include '../sidebar.php'; ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Backup Management</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php 
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Backup Options -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card backup-card">
                                <div class="card-header">
                                    <h3 class="card-title">Create Database Backup</h3>
                                </div>
                                <div class="card-body">
                                    <p>Create a backup of the entire database.</p>
                                    <form method="post">
                                        <input type="hidden" name="backup_type" value="database">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-database mr-2"></i>Backup Database
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card backup-card">
                                <div class="card-header">
                                    <h3 class="card-title">Create File Backup</h3>
                                </div>
                                <div class="card-body">
                                    <p>Create a backup of all uploaded files.</p>
                                    <form method="post">
                                        <input type="hidden" name="backup_type" value="files">
                                        <button type="submit" class="btn btn-success btn-block">
                                            <i class="fas fa-file-archive mr-2"></i>Backup Files
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card backup-card">
                                <div class="card-header">
                                    <h3 class="card-title">Create Full Backup</h3>
                                </div>
                                <div class="card-body">
                                    <p>Create a backup of both database and files.</p>
                                    <form method="post">
                                        <input type="hidden" name="backup_type" value="both">
                                        <button type="submit" class="btn btn-info btn-block">
                                            <i class="fas fa-save mr-2"></i>Full Backup
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Backup List -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Database Backups</h3>
                                </div>
                                <div class="card-body backup-list">
                                    <?php if (!empty($backups['database'])): ?>
                                        <?php foreach ($backups['database'] as $backup): ?>
                                            <div class="backup-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-database mr-2"></i>
                                                        <?php echo $backup; ?>
                                                    </div>
                                                    <div>
                                                        <a href="../backups/<?php echo $backup; ?>" class="btn btn-sm btn-primary" download>
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-center">No database backups found</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">File Backups</h3>
                                </div>
                                <div class="card-body backup-list">
                                    <?php if (!empty($backups['files'])): ?>
                                        <?php foreach ($backups['files'] as $backup): ?>
                                            <div class="backup-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file-archive mr-2"></i>
                                                        <?php echo $backup; ?>
                                                    </div>
                                                    <div>
                                                        <a href="../backups/<?php echo $backup; ?>" class="btn btn-sm btn-primary" download>
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-center">No file backups found</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
</body>
</html> 