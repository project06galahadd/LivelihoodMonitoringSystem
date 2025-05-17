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

// Get household case records for the user
try {
    // Check if table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'tbl_household_case_records'");
    if ($table_check->num_rows == 0) {
        // Table doesn't exist, create it
        $sql = "CREATE TABLE IF NOT EXISTS tbl_household_case_records (
            id INT PRIMARY KEY AUTO_INCREMENT,
            submitted_by INT NOT NULL,
            beneficiary_lastname VARCHAR(100) NOT NULL,
            beneficiary_firstname VARCHAR(100) NOT NULL,
            beneficiary_middlename VARCHAR(100),
            beneficiary_relationship VARCHAR(100),
            age INT,
            birth_date DATE,
            marital_status VARCHAR(50),
            educational_attainment VARCHAR(100),
            occupation VARCHAR(100),
            family_lastname VARCHAR(100),
            family_firstname VARCHAR(100),
            family_middlename VARCHAR(100),
            family_age INT,
            family_birth_date DATE,
            family_birth_place VARCHAR(100),
            family_sex VARCHAR(10),
            complete_address TEXT,
            sitio_purok VARCHAR(100),
            barangay VARCHAR(100),
            town VARCHAR(100),
            province VARCHAR(100),
            marital_status_family VARCHAR(50),
            religion VARCHAR(100),
            sector VARCHAR(100),
            educational_attainment_family VARCHAR(100),
            occupation_family VARCHAR(100),
            estimated_monthly_income DECIMAL(10,2),
            contact_number VARCHAR(20),
            problem_presented TEXT,
            status ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
            remarks TEXT,
            date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            date_approved TIMESTAMP NULL,
            approved_by INT,
            FOREIGN KEY (submitted_by) REFERENCES tbl_members(MEMID) ON DELETE CASCADE,
            FOREIGN KEY (approved_by) REFERENCES tbl_users(user_id) ON DELETE SET NULL
        )";
        
        if (!$conn->query($sql)) {
            throw new Exception("Error creating table: " . $conn->error);
        }
    }

    // Now proceed with the select query
    $sql = "SELECT * FROM tbl_household_case_records WHERE submitted_by = ? ORDER BY date_created DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    error_log("Error in household case records: " . $e->getMessage());
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Household Case Records | MSWD</title>
    <link rel="icon" type="image/png" href="/LivelihoodMonitoringSystem/dist/img/Logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
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
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="home.php" class="brand-link">
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
                            <a href="household_case.php" class="nav-link active">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Household Case Records</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="livelihood.php" class="nav-link">
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
                            <h1 class="m-0">Household Case Records</h1>
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
                                    <h3 class="card-title">My Household Case Records</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCaseModal">
                                            <i class="fas fa-plus"></i> Add New Case
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Date Submitted</th>
                                                <th>Beneficiary Name</th>
                                                <th>Address</th>
                                                <th>Problem Presented</th>
                                                <th>Status</th>
                                                <th>Remarks</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result && $result->num_rows > 0): ?>
                                                <?php while ($row = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?php echo date('M d, Y', strtotime($row['date_created'])); ?></td>
                                                        <td>
                                                            <?php 
                                                            echo htmlspecialchars($row['beneficiary_lastname'] . ', ' . 
                                                                $row['beneficiary_firstname'] . ' ' . 
                                                                ($row['beneficiary_middlename'] ? $row['beneficiary_middlename'] : ''));
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            echo htmlspecialchars($row['complete_address'] . ', ' . 
                                                                $row['sitio_purok'] . ', ' . 
                                                                $row['barangay'] . ', ' . 
                                                                $row['town'] . ', ' . 
                                                                $row['province']);
                                                            ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['problem_presented']); ?></td>
                                                        <td>
                                                            <span class="badge badge-<?php 
                                                                echo $row['status'] === 'PENDING' ? 'warning' : 
                                                                    ($row['status'] === 'APPROVED' ? 'success' : 'danger'); 
                                                            ?>">
                                                                <?php echo htmlspecialchars($row['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($row['remarks'] ?? 'No remarks'); ?></td>
                                                        <td>
                                                            <?php if ($row['status'] === 'PENDING'): ?>
                                                                <button type="button" class="btn btn-sm btn-primary edit-case" data-id="<?php echo $row['id']; ?>">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <button type="button" class="btn btn-sm btn-info view-details" data-id="<?php echo $row['id']; ?>">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No records found</td>
                                                </tr>
                                            <?php endif; ?>
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

    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Case Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="caseDetails"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Case Modal -->
    <div class="modal fade" id="addCaseModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Case</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="caseForm" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="beneficiary_lastname" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="beneficiary_lastname" name="beneficiary_lastname" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="beneficiary_firstname" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="beneficiary_firstname" name="beneficiary_firstname" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="beneficiary_middlename" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="beneficiary_middlename" name="beneficiary_middlename">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="beneficiary_relationship" class="form-label">Relationship *</label>
                                <input type="text" class="form-control" id="beneficiary_relationship" name="beneficiary_relationship" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="age" class="form-label">Age *</label>
                                <input type="number" class="form-control" id="age" name="age" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="birth_date" class="form-label">Birth Date *</label>
                                <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="marital_status" class="form-label">Marital Status *</label>
                                <select class="form-select" id="marital_status" name="marital_status" required>
                                    <option value="">Select status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="educational_attainment" class="form-label">Educational Attainment *</label>
                                <select class="form-select" id="educational_attainment" name="educational_attainment" required>
                                    <option value="">Select education</option>
                                    <option value="Elementary">Elementary</option>
                                    <option value="High School">High School</option>
                                    <option value="Vocational">Vocational</option>
                                    <option value="College">College</option>
                                    <option value="Post Graduate">Post Graduate</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="occupation" class="form-label">Occupation *</label>
                            <input type="text" class="form-control" id="occupation" name="occupation" required>
                        </div>
                        <div class="mb-3">
                            <label for="complete_address" class="form-label">Complete Address *</label>
                            <input type="text" class="form-control" id="complete_address" name="complete_address" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sitio_purok" class="form-label">Sitio/Purok *</label>
                                <input type="text" class="form-control" id="sitio_purok" name="sitio_purok" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="barangay" class="form-label">Barangay *</label>
                                <input type="text" class="form-control" id="barangay" name="barangay" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="town" class="form-label">Town/City *</label>
                                <input type="text" class="form-control" id="town" name="town" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="province" class="form-label">Province *</label>
                                <input type="text" class="form-control" id="province" name="province" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="estimated_monthly_income" class="form-label">Estimated Monthly Income *</label>
                                <input type="number" class="form-control" id="estimated_monthly_income" name="estimated_monthly_income" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="problem_presented" class="form-label">Problem Presented *</label>
                            <textarea class="form-control" id="problem_presented" name="problem_presented" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitCase">Submit Case</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print"]
            });

            // View Details
            $('.view-details').click(function() {
                const recordId = $(this).data('id');
                $.ajax({
                    url: 'get_case.php',
                    method: 'GET',
                    data: { id: recordId },
                    success: function(response) {
                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            if (result.success) {
                                $('#caseDetails').html(result.html);
                                $('#viewDetailsModal').modal('show');
                            } else {
                                alert(result.message || 'Error loading case details');
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('Error loading case details');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('Error loading case details');
                    }
                });
            });

            // Edit case
            $('.edit-case').click(function() {
                const recordId = $(this).data('id');
                window.location.href = 'process_case.php?id=' + recordId;
            });

            // Form submission
            $('#submitCase').click(function() {
                const form = document.getElementById('caseForm');
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }

                const formData = new FormData(form);
                
                $.ajax({
                    url: 'process_case.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            if (result.success) {
                                alert(result.message);
                                location.reload();
                            } else {
                                alert(result.message || 'An error occurred while processing your request.');
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            alert('An error occurred while processing your request.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        console.log('Response:', xhr.responseText);
                        alert('An error occurred while processing your request.');
                    }
                });
            });
        });
    </script>
</body>
</html> 