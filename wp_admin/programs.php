<?php
session_start();
require_once '../includes/conn.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    header('Location: ../signin.php');
    exit();
}

// Handle program status update
if (isset($_POST['update_status'])) {
    $program_id = $_POST['program_id'];
    $new_status = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE tbl_programs SET status = ? WHERE program_id = ?");
    $stmt->bind_param("si", $new_status, $program_id);
    $stmt->execute();
    $stmt->close();
}

// Handle program deletion
if (isset($_POST['delete_program'])) {
    $program_id = $_POST['program_id'];

    $stmt = $conn->prepare("DELETE FROM tbl_programs WHERE program_id = ?");
    $stmt->bind_param("i", $program_id);
    $stmt->execute();
    $stmt->close();
}

// Get all programs
$query = "SELECT * FROM tbl_programs ORDER BY date_created DESC";
$programs = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Management - Livelihood Monitoring System</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <style>
        .program-card {
            transition: transform 0.3s;
        }

        .program-card:hover {
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
                            <h1>Program Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addProgramModal">
                                <i class="fas fa-plus"></i> Add New Program
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <?php if ($programs && $programs->num_rows > 0): ?>
                            <?php while ($program = $programs->fetch_assoc()): ?>
                                <div class="col-md-4">
                                    <div class="card program-card">
                                        <div class="card-header">
                                            <h3 class="card-title"><?php echo htmlspecialchars($program['program_name']); ?></h3>
                                            <div class="card-tools">
                                                <span class="badge badge-<?php echo $program['status'] === 'ACTIVE' ? 'success' : 'danger'; ?>">
                                                    <?php echo $program['status']; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Description:</strong> <?php echo htmlspecialchars($program['description']); ?></p>
                                            <p><strong>Requirements:</strong> <?php echo htmlspecialchars($program['requirements']); ?></p>
                                            <p><strong>Duration:</strong> <?php echo htmlspecialchars($program['duration']); ?></p>
                                            <p><strong>Capacity:</strong> <?php echo htmlspecialchars($program['capacity']); ?> participants</p>
                                        </div>
                                        <div class="card-footer">
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#editProgramModal<?php echo $program['program_id']; ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteProgramModal<?php echo $program['program_id']; ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="program_id" value="<?php echo $program['program_id']; ?>">
                                                <input type="hidden" name="new_status" value="<?php echo $program['status'] === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE'; ?>">
                                                <button type="submit" name="update_status" class="btn btn-<?php echo $program['status'] === 'ACTIVE' ? 'warning' : 'success'; ?> btn-sm">
                                                    <i class="fas fa-<?php echo $program['status'] === 'ACTIVE' ? 'pause' : 'play'; ?>"></i>
                                                    <?php echo $program['status'] === 'ACTIVE' ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Program Modal -->
                                <div class="modal fade" id="editProgramModal<?php echo $program['program_id']; ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Program</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <form method="post" action="update_program.php">
                                                <div class="modal-body">
                                                    <input type="hidden" name="program_id" value="<?php echo $program['program_id']; ?>">
                                                    <div class="form-group">
                                                        <label>Program Name</label>
                                                        <input type="text" class="form-control" name="program_name" value="<?php echo htmlspecialchars($program['program_name']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Description</label>
                                                        <textarea class="form-control" name="description" required><?php echo htmlspecialchars($program['description']); ?></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Requirements</label>
                                                        <textarea class="form-control" name="requirements" required><?php echo htmlspecialchars($program['requirements']); ?></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Duration</label>
                                                        <input type="text" class="form-control" name="duration" value="<?php echo htmlspecialchars($program['duration']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Capacity</label>
                                                        <input type="number" class="form-control" name="capacity" value="<?php echo htmlspecialchars($program['capacity']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Program Modal -->
                                <div class="modal fade" id="deleteProgramModal<?php echo $program['program_id']; ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Program</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this program?</p>
                                                <p><strong><?php echo htmlspecialchars($program['program_name']); ?></strong></p>
                                            </div>
                                            <div class="modal-footer">
                                                <form method="post">
                                                    <input type="hidden" name="program_id" value="<?php echo $program['program_id']; ?>">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="delete_program" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No programs found. Add a new program to get started.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Add Program Modal -->
    <div class="modal fade" id="addProgramModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Program</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form method="post" action="add_program.php">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Program Name</label>
                            <input type="text" class="form-control" name="program_name" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Requirements</label>
                            <textarea class="form-control" name="requirements" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Duration</label>
                            <input type="text" class="form-control" name="duration" required>
                        </div>
                        <div class="form-group">
                            <label>Capacity</label>
                            <input type="number" class="form-control" name="capacity" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.min.js"></script>
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
</body>

</html>