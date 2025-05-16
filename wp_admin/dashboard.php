<?php
session_start();
require_once '../includes/conn.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    header('Location: ../signin.php');
    exit();
}

// Initialize statistics array
$stats = [
    'total_members' => 0,
    'pending_applications' => 0,
    'approved_applications' => 0,
    'rejected_applications' => 0,
    'total_programs' => 0,
    'active_programs' => 0,
    'total_enrollments' => 0,
    'active_enrollments' => 0
];

try {
    // Get total members with prepared statement
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_members");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_members'] = $result->fetch_assoc()['count'];

    // Get application status counts with prepared statement
    $stmt = $conn->prepare("SELECT STATUS_REMARKS, COUNT(*) as count FROM tbl_members GROUP BY STATUS_REMARKS");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        switch ($row['STATUS_REMARKS']) {
            case 'PENDING':
                $stats['pending_applications'] = $row['count'];
                break;
            case 'APPROVED':
                $stats['approved_applications'] = $row['count'];
                break;
            case 'REJECTED':
                $stats['rejected_applications'] = $row['count'];
                break;
        }
    }

    // Get program statistics with prepared statements
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_programs");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_programs'] = $result->fetch_assoc()['count'];

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_programs WHERE status = 'ACTIVE'");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['active_programs'] = $result->fetch_assoc()['count'];

    // Get enrollment statistics
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_enrolled_programs");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_enrollments'] = $result->fetch_assoc()['count'];

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_enrolled_programs WHERE status = 'ACTIVE'");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['active_enrollments'] = $result->fetch_assoc()['count'];

    // Get recent activities with prepared statement
    $stmt = $conn->prepare("
        SELECT m.*, u.username, u.email 
        FROM tbl_members m 
        JOIN tbl_users u ON m.user_id = u.user_id 
        ORDER BY m.DATE_OF_APPLICATION DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recent_activities = $stmt->get_result();

    // Get program distribution with prepared statement
    $stmt = $conn->prepare("
        SELECT DESIRED_LIVELIHOOD_PROGRAM, COUNT(*) as count 
        FROM tbl_members 
        GROUP BY DESIRED_LIVELIHOOD_PROGRAM
    ");
    $stmt->execute();
    $program_distribution = $stmt->get_result();

    // Get recent enrollments
    $stmt = $conn->prepare("
        SELECT e.*, u.username, p.program_name 
        FROM tbl_enrolled_programs e 
        JOIN tbl_users u ON e.user_id = u.user_id 
        JOIN tbl_programs p ON e.program_id = p.program_id 
        ORDER BY e.enrollment_date DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recent_enrollments = $stmt->get_result();
} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while loading the dashboard.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Livelihood Monitoring System</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css">
    <style>
        .dashboard-card {
            transition: transform 0.3s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .stat-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .recent-activity {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s;
        }

        .activity-item:hover {
            background-color: #f8f9fa;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
                            <h1>Dashboard</h1>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-right">
                                <button class="btn btn-primary" onclick="refreshDashboard()">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info stat-card">
                                <div class="inner">
                                    <h3><?php echo $stats['total_members']; ?></h3>
                                    <p>Total Members</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning stat-card">
                                <div class="inner">
                                    <h3><?php echo $stats['pending_applications']; ?></h3>
                                    <p>Pending Applications</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success stat-card">
                                <div class="inner">
                                    <h3><?php echo $stats['approved_applications']; ?></h3>
                                    <p>Approved Applications</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger stat-card">
                                <div class="inner">
                                    <h3><?php echo $stats['rejected_applications']; ?></h3>
                                    <p>Rejected Applications</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Statistics -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary stat-card">
                                <div class="inner">
                                    <h3><?php echo $stats['total_programs']; ?></h3>
                                    <p>Total Programs</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-book stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success stat-card">
                                <div class="inner">
                                    <h3><?php echo $stats['active_programs']; ?></h3>
                                    <p>Active Programs</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-double stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info stat-card">
                                <div class="inner">
                                    <h3><?php echo $stats['total_enrollments']; ?></h3>
                                    <p>Total Enrollments</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-graduate stat-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning stat-card">
                                <div class="inner">
                                    <h3><?php echo $stats['active_enrollments']; ?></h3>
                                    <p>Active Enrollments</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-check stat-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Recent Activities -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Program Distribution</h3>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="programChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Activities</h3>
                                </div>
                                <div class="card-body recent-activity">
                                    <?php if ($recent_activities && $recent_activities->num_rows > 0): ?>
                                        <?php while ($activity = $recent_activities->fetch_assoc()): ?>
                                            <div class="activity-item">
                                                <div class="user-block">
                                                    <span class="username">
                                                        <?php echo htmlspecialchars($activity['FIRSTNAME'] . ' ' . $activity['LASTNAME']); ?>
                                                    </span>
                                                    <span class="description">
                                                        <?php echo date('M d, Y', strtotime($activity['DATE_OF_APPLICATION'])); ?>
                                                    </span>
                                                </div>
                                                <p>
                                                    Applied for <?php echo htmlspecialchars($activity['DESIRED_LIVELIHOOD_PROGRAM']); ?>
                                                </p>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <p class="text-center">No recent activities</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Enrollments -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Enrollments</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Member</th>
                                                    <th>Program</th>
                                                    <th>Enrollment Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($recent_enrollments && $recent_enrollments->num_rows > 0): ?>
                                                    <?php while ($enrollment = $recent_enrollments->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($enrollment['username']); ?></td>
                                                            <td><?php echo htmlspecialchars($enrollment['program_name']); ?></td>
                                                            <td><?php echo date('M d, Y', strtotime($enrollment['enrollment_date'])); ?></td>
                                                            <td>
                                                                <span class="badge badge-<?php
                                                                                            echo $enrollment['status'] === 'ACTIVE' ? 'success' : ($enrollment['status'] === 'PENDING' ? 'warning' : ($enrollment['status'] === 'COMPLETED' ? 'info' : 'danger'));
                                                                                            ?>">
                                                                    <?php echo $enrollment['status']; ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center">No recent enrollments</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" style="display: none;">
        <div class="spinner"></div>
    </div>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script>
        // Program Distribution Chart
        const programData = <?php
                            $programs = [];
                            $counts = [];
                            if ($program_distribution) {
                                while ($row = $program_distribution->fetch_assoc()) {
                                    $programs[] = $row['DESIRED_LIVELIHOOD_PROGRAM'];
                                    $counts[] = $row['count'];
                                }
                            }
                            echo json_encode(['programs' => $programs, 'counts' => $counts]);
                            ?>;

        const programChart = new Chart(document.getElementById('programChart'), {
            type: 'bar',
            data: {
                labels: programData.programs,
                datasets: [{
                    label: 'Number of Applications',
                    data: programData.counts,
                    backgroundColor: 'rgba(60,141,188,0.9)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Applications: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });

        // Refresh Dashboard Function
        function refreshDashboard() {
            $('.loading-overlay').show();
            location.reload();
        }

        // Auto-refresh dashboard every 5 minutes
        setInterval(refreshDashboard, 300000);
    </script>
</body>

</html>