<?php
session_start();
require_once '../includes/conn.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../signin.php');
    exit();
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enrollment_id']) && isset($_POST['status'])) {
    $enrollment_id = $_POST['enrollment_id'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'] ?? '';
    
    try {
        $conn->begin_transaction();
        
        // Update enrollment status
        $query = "UPDATE tbl_enrolled_programs SET status = ? WHERE enrollment_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $enrollment_id);
        $stmt->execute();
        
        // Log status change
        $query = "INSERT INTO tbl_enrollment_history (enrollment_id, status, remarks, updated_by) 
                 VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issi", $enrollment_id, $status, $remarks, $_SESSION['user_id']);
        $stmt->execute();
        
        $conn->commit();
        $_SESSION['success'] = "Enrollment status updated successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error updating enrollment status: " . $e->getMessage();
    }
    
    header('Location: enrollments.php');
    exit();
}

// Get all enrollments with user and program details
$query = "SELECT e.*, u.username, u.email, p.program_name, 
          (SELECT status FROM tbl_enrollment_history 
           WHERE enrollment_id = e.enrollment_id 
           ORDER BY date_updated DESC LIMIT 1) as last_status
          FROM tbl_enrolled_programs e
          JOIN tbl_users u ON e.user_id = u.user_id
          JOIN tbl_programs p ON e.program_id = p.program_id
          ORDER BY e.enrollment_date DESC";
$enrollments = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Enrollments - Livelihood Monitoring System</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
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
                            <h1>Program Enrollments</h1>
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

                    <div class="card">
                        <div class="card-body">
                            <table id="enrollmentsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Program</th>
                                        <th>Member</th>
                                        <th>Enrollment Date</th>
                                        <th>Status</th>
                                        <th>Last Update</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($enrollment = $enrollments->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $enrollment['enrollment_id']; ?></td>
                                            <td><?php echo htmlspecialchars($enrollment['program_name']); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($enrollment['username']); ?><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($enrollment['email']); ?></small>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($enrollment['enrollment_date'])); ?></td>
                                            <td>
                                                <span class="badge badge-<?php 
                                                    echo $enrollment['status'] === 'ACTIVE' ? 'success' : 
                                                        ($enrollment['status'] === 'PENDING' ? 'warning' : 
                                                        ($enrollment['status'] === 'COMPLETED' ? 'info' : 'danger')); 
                                                ?>">
                                                    <?php echo $enrollment['status']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $enrollment['last_status']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" 
                                                        data-toggle="modal" 
                                                        data-target="#updateModal<?php echo $enrollment['enrollment_id']; ?>">
                                                    <i class="fas fa-edit"></i> Update Status
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Update Status Modal -->
                                        <div class="modal fade" id="updateModal<?php echo $enrollment['enrollment_id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update Enrollment Status</h5>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['enrollment_id']; ?>">
                                                            
                                                            <div class="form-group">
                                                                <label>Status</label>
                                                                <select name="status" class="form-control" required>
                                                                    <option value="PENDING" <?php echo $enrollment['status'] === 'PENDING' ? 'selected' : ''; ?>>Pending</option>
                                                                    <option value="ACTIVE" <?php echo $enrollment['status'] === 'ACTIVE' ? 'selected' : ''; ?>>Active</option>
                                                                    <option value="COMPLETED" <?php echo $enrollment['status'] === 'COMPLETED' ? 'selected' : ''; ?>>Completed</option>
                                                                    <option value="REJECTED" <?php echo $enrollment['status'] === 'REJECTED' ? 'selected' : ''; ?>>Rejected</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="form-group">
                                                                <label>Remarks</label>
                                                                <textarea name="remarks" class="form-control" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Update Status</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function () {
            $('#enrollmentsTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
</body>
</html> 