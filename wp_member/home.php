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

// Get user profile data and status
try {
    $stmt = $conn->prepare("SELECT u.*, m.STATUS_REMARKS as status FROM tbl_users u
                           LEFT JOIN tbl_members m ON u.user_id = m.user_id
                           WHERE u.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();

    // Set status in session if not already set
    if (!isset($_SESSION['status'])) {
        $_SESSION['status'] = $user_data['status'] ?? 'PENDING';
    }
} catch (Exception $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    $user_data = null;
}

// Function to check if table exists
function tableExists($conn, $tableName)
{
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}

// Function to count records with table existence check
function getCount($conn, $table, $condition = '1')
{
    if (!tableExists($conn, $table)) {
        return 0;
    }
    $sql = "SELECT COUNT(*) AS total FROM $table WHERE $condition";
    $result = $conn->query($sql);
    if (!$result) {
        error_log("Query error for table $table: " . $conn->error);
        return 0;
    }
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Get user's specific counts with error handling
try {
    $livelihood_count = getCount($conn, 'tbl_livelihood_records', "user_id = $user_id");
    $household_count = getCount($conn, 'tbl_household_records', "user_id = $user_id");
    $activity_count = getCount($conn, 'tbl_activity_log', "user_id = $user_id");

    // Get recent activities
    if (tableExists($conn, 'tbl_activity_log')) {
        $recent_activities = $conn->query("
            SELECT activity, status, created_at
            FROM tbl_activity_log
            WHERE user_id = $user_id
            ORDER BY created_at DESC
            LIMIT 5
        ");
        if (!$recent_activities) {
            error_log("Recent activities query error: " . $conn->error);
            $recent_activities = null;
        }
    } else {
        $recent_activities = null;
    }
} catch (Exception $e) {
    error_log("Error in dashboard queries: " . $e->getMessage());
    $livelihood_count = 0;
    $household_count = 0;
    $activity_count = 0;
    $recent_activities = null;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | MSWD</title>
    <link rel="icon" type="image/png" href="/LivelihoodMonitoringSystem/dist/img/Logo.png">
    <!-- Google Font: Source Sans Pro -->
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

        body {
            font-family: 'Source Sans Pro', sans-serif;
            background: var(--light-bg);
            position: relative;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .main-sidebar {
            background: var(--primary-color);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .brand-link {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff !important;
            background: var(--primary-color);
            padding: 1rem;
        }

        .brand-link img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .user-panel {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
        }

        .user-panel .image img {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .user-panel .info a {
            color: #fff;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .user-panel .info small {
            color: rgba(255, 255, 255, 0.7);
        }

        .nav-sidebar .nav-item {
            margin: 5px 10px;
        }

        .nav-sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .nav-sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .nav-sidebar .nav-link.active {
            background: var(--secondary-color);
            color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .nav-sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Header Styles */
        .main-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .main-header .nav-link {
            color: var(--text-color) !important;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: var(--secondary-color);
            color: #fff;
        }

        /* Content Styles */
        .content-wrapper {
            background: var(--light-bg);
        }

        .content-header {
            padding: 1.5rem 1rem;
            background: transparent;
        }

        .content-header h1 {
            color: var(--text-color);
            font-weight: 600;
            font-size: 1.8rem;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: var(--secondary-color);
        }

        .breadcrumb-item.active {
            color: var(--text-color);
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            background: #fff;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 1rem 1.5rem;
            border-radius: 10px 10px 0 0;
        }

        .card-title {
            color: var(--text-color);
            font-weight: 600;
            margin: 0;
        }

        .info-box {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            background: #fff;
            min-height: 120px;
        }

        .info-box:hover {
            transform: translateY(-5px);
        }

        .info-box-icon {
            border-radius: 10px 0 0 10px;
            background: var(--secondary-color);
            color: #fff;
            font-size: 1.5rem;
        }

        .info-box-content {
            padding: 1.5rem;
        }

        .info-box-text {
            font-weight: 600;
            color: var(--text-color);
            font-size: 1rem;
        }

        .info-box-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--secondary-color);
        }

        /* Alert Styles */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .main-sidebar {
                transform: translateX(-100%);
            }

            .sidebar-open .main-sidebar {
                transform: translateX(0);
            }

            .content-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
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
            <a href="home.php" class="brand-link text-center">
                <img src="/LivelihoodMonitoringSystem/dist/img/Logo.png" alt="MSWD Logo" class="brand-image img-circle elevation-3">
                <span class="brand-text font-weight-light">MSWD Member</span>
            </a>
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="<?php echo !empty($user_data['profile_picture']) ? '../uploads/profile/' . $user_data['profile_picture'] : '/LivelihoodMonitoringSystem/dist/img/default-avatar.png'; ?>"
                        class="img-circle elevation-2" alt="User Image" style="width:70px;height:70px;object-fit:cover;">
                </div>
                <div class="info">
                    <a href="profile.php" class="d-block" style="font-size:1.2rem;font-weight:600;">
                        <?php echo htmlspecialchars($fullname); ?>
                    </a>
                    <small>
                        <i class="fas fa-circle text-success"></i> Online
                    </small>
                </div>
            </div>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="home.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-home"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="livelihood.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'livelihood.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-chart-line"></i>
                                <p>Livelihood Records</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="household_case.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'household_case.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Household Case Records</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="news.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'news.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-newspaper"></i>
                                <p>News & Announcements</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="chat.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-comments"></i>
                                <p>Chat with Admin</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Profile Settings</p>
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
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-8 col-12 mb-2 mb-md-0">
                            <h1 class="m-0" style="font-weight:700;font-size:2.2rem;">Welcome, <?php echo htmlspecialchars($fullname); ?>!</h1>
                            <p class="text-muted" style="font-size:1.1rem;">Here is your personalized dashboard. Quick access to your records and activities.</p>
                        </div>
                        <div class="col-md-4 col-12 text-md-right text-center">
                            <a href="livelihood.php" class="btn btn-gradient-primary m-1"><i class="fas fa-plus mr-1"></i> Add Livelihood</a>
                            <a href="household_case.php" class="btn btn-gradient-success m-1"><i class="fas fa-plus mr-1"></i> Add Household Case</a>
                            <a href="profile.php" class="btn btn-gradient-info m-1"><i class="fas fa-user mr-1"></i> View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    <?php if (isset($_SESSION['status']) && $_SESSION['status'] == 'PENDING'): ?>
                        <div class="alert alert-warning alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-warning"></i> Account Pending Approval</h5>
                            Your account is currently pending approval. You will have full access once an administrator approves your account.
                        </div>
                    <?php endif; ?>
                    <!-- Info Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-6 mb-3">
                            <a href="livelihood.php" class="card card-elegant h-100 text-decoration-none">
                                <div class="card-body d-flex align-items-center">
                                    <div class="icon-circle bg-gradient-primary text-white mr-3"><i class="fas fa-chart-line fa-2x"></i></div>
                                    <div>
                                        <div class="card-title mb-0" style="font-size:1.1rem;font-weight:600;">Livelihood Records</div>
                                        <div class="display-4" style="font-weight:700;"><?php echo $livelihood_count; ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="household_case.php" class="card card-elegant h-100 text-decoration-none">
                                <div class="card-body d-flex align-items-center">
                                    <div class="icon-circle bg-gradient-success text-white mr-3"><i class="fas fa-users fa-2x"></i></div>
                                    <div>
                                        <div class="card-title mb-0" style="font-size:1.1rem;font-weight:600;">Household Cases</div>
                                        <div class="display-4" style="font-weight:700;"><?php echo $household_count; ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="news.php" class="card card-elegant h-100 text-decoration-none">
                                <div class="card-body d-flex align-items-center">
                                    <div class="icon-circle bg-gradient-warning text-white mr-3"><i class="fas fa-newspaper fa-2x"></i></div>
                                    <div>
                                        <div class="card-title mb-0" style="font-size:1.1rem;font-weight:600;">News & Updates</div>
                                        <div class="display-4" style="font-weight:700;"><?php echo $activity_count; ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="profile.php" class="card card-elegant h-100 text-decoration-none">
                                <div class="card-body d-flex align-items-center">
                                    <div class="icon-circle bg-gradient-info text-white mr-3"><i class="fas fa-eye fa-2x"></i></div>
                                    <div>
                                        <div class="card-title mb-0" style="font-size:1.1rem;font-weight:600;">Profile Views</div>
                                        <div class="display-4" style="font-weight:700;">0</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <!-- Recent Activity Timeline -->
                    <div class="row">
                        <div class="col-lg-8 col-12 mb-4 mb-lg-0">
                            <div class="card card-elegant">
                                <div class="card-header bg-white border-0 pb-0">
                                    <h3 class="card-title" style="font-weight:600;"><i class="fas fa-history mr-2"></i>Recent Activity</h3>
                                </div>
                                <div class="card-body pt-3">
                                    <?php if ($recent_activities && $recent_activities->num_rows > 0): ?>
                                        <ul class="timeline timeline-elegant">
                                            <?php while ($activity = $recent_activities->fetch_assoc()): ?>
                                                <li class="timeline-item mb-4">
                                                    <span class="timeline-icon bg-gradient-primary"><i class="fas fa-check"></i></span>
                                                    <div class="timeline-content">
                                                        <span class="badge badge-<?php echo strtolower($activity['status']) === 'success' ? 'success' : (strtolower($activity['status']) === 'pending' ? 'warning' : 'danger'); ?> mr-2">
                                                            <?php echo htmlspecialchars($activity['status']); ?>
                                                        </span>
                                                        <span class="font-weight-bold"><?php echo htmlspecialchars($activity['activity']); ?></span>
                                                        <div class="small text-muted mt-1"><i class="far fa-clock mr-1"></i><?php echo date('M d, Y h:i A', strtotime($activity['created_at'])); ?></div>
                                                    </div>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div class="text-center text-muted">
                                            <i class="fas fa-clock fa-3x mb-3"></i>
                                            <p>No recent activity to display</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-12">
                            <div class="card card-elegant h-100">
                                <div class="card-header bg-white border-0 pb-0">
                                    <h3 class="card-title" style="font-weight:600;"><i class="fas fa-info-circle mr-2"></i>Quick Tips</h3>
                                </div>
                                <div class="card-body pt-3">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-3"><i class="fas fa-lightbulb text-warning mr-2"></i>Keep your profile updated for better service.</li>
                                        <li class="mb-3"><i class="fas fa-bell text-info mr-2"></i>Check News & Announcements regularly.</li>
                                        <li class="mb-3"><i class="fas fa-users text-success mr-2"></i>Submit household cases for assistance.</li>
                                        <li><i class="fas fa-comments text-primary mr-2"></i>Use chat to contact admin for help.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <style>
        .btn-gradient-primary {background: linear-gradient(90deg,#3498db 0%,#6dd5fa 100%);color:#fff;border:none;}
        .btn-gradient-success {background: linear-gradient(90deg,#28a745 0%,#6dd5fa 100%);color:#fff;border:none;}
        .btn-gradient-info {background: linear-gradient(90deg,#17a2b8 0%,#6dd5fa 100%);color:#fff;border:none;}
        .card-elegant {border-radius:18px;box-shadow:0 4px 24px rgba(44,62,80,0.08);transition:box-shadow .3s,transform .3s;}
        .card-elegant:hover {box-shadow:0 8px 32px rgba(44,62,80,0.16);transform:translateY(-4px) scale(1.01);}
        .icon-circle {width:56px;height:56px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:2rem;box-shadow:0 2px 8px rgba(44,62,80,0.08);margin-right:1rem;}
        .bg-gradient-primary {background:linear-gradient(135deg,#3498db 0%,#2c3e50 100%)!important;}
        .bg-gradient-success {background:linear-gradient(135deg,#28a745 0%,#2ecc71 100%)!important;}
        .bg-gradient-warning {background:linear-gradient(135deg,#f1c40f 0%,#e67e22 100%)!important;}
        .bg-gradient-info {background:linear-gradient(135deg,#17a2b8 0%,#2980b9 100%)!important;}
        .timeline-elegant {list-style:none;padding-left:0;position:relative;}
        .timeline-elegant:before {content:'';position:absolute;left:28px;top:0;bottom:0;width:4px;background:#e9ecef;border-radius:2px;}
        .timeline-item {position:relative;min-height:60px;}
        .timeline-icon {position:absolute;left:10px;top:0;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:50%;color:#fff;font-size:1.2rem;box-shadow:0 2px 8px rgba(44,62,80,0.08);z-index:1;}
        .timeline-content {margin-left:60px;}
        @media (max-width: 767.98px) {
            .timeline-elegant:before {left:18px;}
            .timeline-icon {left:0;}
            .timeline-content {margin-left:48px;}
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
</body>

</html>