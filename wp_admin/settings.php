<?php
session_start();
require_once '../includes/conn.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    header('Location: ../signin.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_settings'])) {
        try {
            // Update system settings
            $stmt = $conn->prepare("UPDATE tbl_system_setting SET 
                SYS_NAME = ?,
                SYS_EMAIL = ?,
                SYS_ADDRESS = ?,
                SYS_ABOUT = ?,
                SYS_ISDEFAULT = ?
                WHERE SYS_ID = ?");
            $stmt->bind_param(
                "ssssss",
                $_POST['system_name'],
                $_POST['system_email'],
                $_POST['system_address'],
                $_POST['system_about'],
                $_POST['is_default'],
                $_POST['sys_id']
            );
            $stmt->execute();

            $_SESSION['success'] = "Settings updated successfully";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error updating settings: " . $e->getMessage();
        }
        header('Location: settings.php');
        exit();
    }
}

// Get current settings
$settings = [];
$result = $conn->query("SELECT * FROM tbl_system_setting");
if ($result->num_rows > 0) {
    $settings = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Livelihood Monitoring System</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <style>
        .settings-card {
            transition: transform 0.3s;
        }

        .settings-card:hover {
            transform: translateY(-5px);
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
                            <h1>System Settings</h1>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card settings-card">
                                <div class="card-header">
                                    <h3 class="card-title">System Information</h3>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <input type="hidden" name="sys_id" value="<?php echo htmlspecialchars($settings['SYS_ID'] ?? ''); ?>">
                                        <div class="form-group">
                                            <label>System Name</label>
                                            <input type="text" class="form-control" name="system_name"
                                                value="<?php echo htmlspecialchars($settings['SYS_NAME'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>System Email</label>
                                            <input type="email" class="form-control" name="system_email"
                                                value="<?php echo htmlspecialchars($settings['SYS_EMAIL'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>System Address</label>
                                            <textarea class="form-control" name="system_address" rows="3" required><?php
                                                                                                                    echo htmlspecialchars($settings['SYS_ADDRESS'] ?? '');
                                                                                                                    ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>About System</label>
                                            <textarea class="form-control" name="system_about" rows="5" required><?php
                                                                                                                    echo htmlspecialchars($settings['SYS_ABOUT'] ?? '');
                                                                                                                    ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Set as Default</label>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="default_yes" name="is_default" value="YES"
                                                    <?php echo ($settings['SYS_ISDEFAULT'] ?? '') === 'YES' ? 'checked' : ''; ?> class="custom-control-input">
                                                <label class="custom-control-label" for="default_yes">Yes</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="default_no" name="is_default" value="NO"
                                                    <?php echo ($settings['SYS_ISDEFAULT'] ?? '') === 'NO' ? 'checked' : ''; ?> class="custom-control-input">
                                                <label class="custom-control-label" for="default_no">No</label>
                                            </div>
                                        </div>
                                        <button type="submit" name="update_settings" class="btn btn-primary">
                                            <i class="fas fa-save mr-2"></i>Save Changes
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card settings-card">
                                <div class="card-header">
                                    <h3 class="card-title">System Maintenance</h3>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        <a href="backup.php" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">
                                                    <i class="fas fa-database mr-2"></i>Backup Management
                                                </h5>
                                                <small class="text-muted">
                                                    <i class="fas fa-chevron-right"></i>
                                                </small>
                                            </div>
                                            <p class="mb-1">Manage database and file backups</p>
                                        </a>
                                        <a href="logs.php" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">
                                                    <i class="fas fa-history mr-2"></i>System Logs
                                                </h5>
                                                <small class="text-muted">
                                                    <i class="fas fa-chevron-right"></i>
                                                </small>
                                            </div>
                                            <p class="mb-1">View system activity logs</p>
                                        </a>
                                    </div>
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