<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if user is logged in and has member role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'MEMBER') {
    header('Location: signin.php');
    exit();
}

require_once "../wp_admin/includes/conn.php";

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$fullname = $_SESSION['fullname'];

// Get livelihood programs
try {
    $sql = "SELECT * FROM tbl_livelihood WHERE STATUS = 'ACTIVE' ORDER BY LIVELIHOOD_NAME ASC";
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Error executing query: " . $conn->error);
        }
} catch (Exception $e) {
    error_log("Error in livelihood programs: " . $e->getMessage());
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livelihood Programs | MSWD</title>
    <link rel="icon" type="image/png" href="/LivelihoodMonitoringSystem/dist/img/Logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
            --dark-bg: #2c3e50;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
        }
        body { font-family: 'Source Sans Pro', sans-serif; background: var(--light-bg); position: relative; overflow-x: hidden; }
        .main-sidebar { background: var(--primary-color); box-shadow: 2px 0 10px rgba(0,0,0,0.1); transition: all 0.3s ease; }
        .brand-link { border-bottom: 1px solid rgba(255,255,255,0.1); color: #fff !important; background: var(--primary-color); padding: 1rem; }
        .brand-link img { width: 40px; height: 40px; margin-right: 10px; }
        .user-panel { border-bottom: 1px solid rgba(255,255,255,0.1); padding: 1.5rem 1rem; background: rgba(255,255,255,0.05); }
        .user-panel .image img { width: 60px; height: 60px; border: 3px solid rgba(255,255,255,0.2); }
        .user-panel .info a { color: #fff; font-weight: 600; font-size: 1.1rem; }
        .user-panel .info small { color: rgba(255,255,255,0.7); }
        .nav-sidebar .nav-item { margin: 5px 10px; }
        .nav-sidebar .nav-link { color: rgba(255,255,255,0.8); border-radius: 8px; padding: 12px 15px; transition: all 0.3s ease; display: flex; align-items: center; }
        .nav-sidebar .nav-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .nav-sidebar .nav-link.active { background: var(--secondary-color); color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .nav-sidebar .nav-link i { margin-right: 10px; width: 20px; text-align: center; }
        .main-header { background: #fff; border-bottom: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .main-header .nav-link { color: var(--text-color) !important; }
        .dropdown-menu { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 8px; }
        .dropdown-item { padding: 10px 20px; transition: all 0.3s ease; }
        .dropdown-item:hover { background: var(--secondary-color); color: #fff; }
        .content-wrapper { background: var(--light-bg); }
        .content-header { padding: 1.5rem 1rem; background: transparent; }
        .content-header h1 { color: var(--text-color); font-weight: 600; font-size: 1.8rem; }
        .breadcrumb { background: transparent; padding: 0; }
        .breadcrumb-item a { color: var(--secondary-color); }
        .breadcrumb-item.active { color: var(--text-color); }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 1.5rem; background: #fff; }
        .card-header { background: #fff; border-bottom: 1px solid #eee; padding: 1rem 1.5rem; border-radius: 10px 10px 0 0; }
        .card-title { color: var(--text-color); font-weight: 600; margin: 0; }
        @media (max-width: 768px) { .main-sidebar { transform: translateX(-100%); } .sidebar-open .main-sidebar { transform: translateX(0); } .content-wrapper { margin-left: 0; } }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <!-- Video Background -->
    <video autoplay muted loop id="myVideo" class="video-background">
        <source src="/LivelihoodMonitoringSystem/dist/video/background.mp4" type="video/mp4">
    </video>

    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                        <i class="fas fa-user-circle mr-2"></i><?php echo htmlspecialchars($fullname); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-light-primary elevation-2">
            <a href="home.php" class="brand-link text-center">
                <img src="/LivelihoodMonitoringSystem/dist/img/Logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">MSWD</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="home.php" class="nav-link">
                                <i class="nav-icon fas fa-home"></i>
                                <p>Home</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="household_case.php" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Household Case Records</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="livelihood.php" class="nav-link active">
                                <i class="nav-icon fas fa-briefcase"></i>
                                <p>Livelihood Programs</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="news.php" class="nav-link">
                                <i class="nav-icon fas fa-newspaper"></i>
                                <p>News & Announcements</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Livelihood Programs</h1>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Available Livelihood Programs</h3>
                                </div>
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Program Name</th>
                                                <th>Description</th>
                                                <th>Date Created</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['LIVELIHOOD_NAME']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['LIVELIHOOD_DESCRIPTION']); ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($row['LIVELIHOOD_CREATED'])); ?></td>
                                                        <td><span class="status-badge status-active">Active</span></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-primary" onclick="applyProgram(<?php echo $row['LIVELIHOOD_ID']; ?>)">
                                                                <i class="fas fa-check"></i> Apply
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                echo "<tr><td colspan='5' class='text-center'>No livelihood programs available</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Apply Program Modal -->
    <div class="modal fade" id="applyProgramModal" tabindex="-1" role="dialog" aria-labelledby="applyProgramModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyProgramModalLabel">Apply for Livelihood Program</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="applyProgramForm" action="process_livelihood.php" method="POST">
                    <input type="hidden" name="program_id" id="apply_program_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="experience">Experience Level</label>
                            <select class="form-control" id="experience" name="experience" required>
                                <option value="">Select Experience Level</option>
                                <option value="BEGINNER">Beginner</option>
                                <option value="INTERMEDIATE">Intermediate</option>
                                <option value="ADVANCED">Advanced</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="current_situation">Current Livelihood Situation</label>
                            <input type="text" class="form-control" id="current_situation" name="current_situation" required>
                        </div>
                        <div class="form-group">
                            <label for="willing_training">Are you willing to commit to the required training?</label>
                            <select class="form-control" id="willing_training" name="willing_training" required>
                                <option value="">Select Option</option>
                                <option value="YES">Yes</option>
                                <option value="NO">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="reason">Why are you interested in this program?</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../plugins/jszip/jszip.min.js"></script>
    <script src="../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
    <script>
    $(function () {
        $('#example1').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    function applyProgram(id) {
        $('#apply_program_id').val(id);
        $('#applyProgramModal').modal('show');
        }

    // Form Validation
    $(document).ready(function() {
        $('#applyProgramForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if(result.success) {
                            alert(result.message);
                            location.reload();
                        } else {
                            alert(result.message || 'Error processing request');
                        }
                    } catch(e) {
                        alert('Error processing request');
                    }
                },
                error: function() {
                    alert('Error processing request');
        }
            });
        });
    });
    </script>
</body>
</html> 