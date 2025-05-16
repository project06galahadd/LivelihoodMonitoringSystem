<?php
session_start();
require_once '../includes/conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../signin.php');
    exit();
}

// Get available programs
$query = "SELECT p.*, 
          (SELECT COUNT(*) FROM tbl_enrolled_programs ep 
           WHERE ep.program_id = p.program_id 
           AND ep.status IN ('PENDING', 'ACTIVE')) as enrolled_count 
          FROM tbl_programs p 
          WHERE p.status = 'ACTIVE' 
          ORDER BY p.date_created DESC";
$programs = $conn->query($query);

// Get user's enrolled programs
$user_id = $_SESSION['user_id'];
$query = "SELECT program_id FROM tbl_enrolled_programs WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$enrolled_programs = [];
while ($row = $result->fetch_assoc()) {
    $enrolled_programs[] = $row['program_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Programs - Livelihood Monitoring System</title>
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <style>
        .program-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .program-card:hover {
            transform: translateY(-5px);
        }
        .program-header {
            background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
            color: white;
            padding: 1rem;
        }
        .capacity-bar {
            height: 5px;
            background-color: #e9ecef;
            border-radius: 2px;
            margin-top: 0.5rem;
        }
        .capacity-fill {
            height: 100%;
            background-color: #28a745;
            border-radius: 2px;
            transition: width 0.3s ease;
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
                            <h1>Available Programs</h1>
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
                        <?php if ($programs && $programs->num_rows > 0): ?>
                            <?php while ($program = $programs->fetch_assoc()): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card program-card">
                                        <div class="program-header">
                                            <h3 class="card-title"><?php echo htmlspecialchars($program['program_name']); ?></h3>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Description:</strong> <?php echo htmlspecialchars($program['description']); ?></p>
                                            <p><strong>Requirements:</strong> <?php echo htmlspecialchars($program['requirements']); ?></p>
                                            <p><strong>Duration:</strong> <?php echo htmlspecialchars($program['duration']); ?></p>
                                            
                                            <div class="capacity-info">
                                                <p><strong>Capacity:</strong> <?php echo $program['enrolled_count']; ?>/<?php echo $program['capacity']; ?> enrolled</p>
                                                <div class="capacity-bar">
                                                    <div class="capacity-fill" style="width: <?php echo ($program['enrolled_count'] / $program['capacity']) * 100; ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <?php if (in_array($program['program_id'], $enrolled_programs)): ?>
                                                <button class="btn btn-secondary btn-block" disabled>
                                                    <i class="fas fa-check"></i> Already Enrolled
                                                </button>
                                            <?php elseif ($program['enrolled_count'] >= $program['capacity']): ?>
                                                <button class="btn btn-danger btn-block" disabled>
                                                    <i class="fas fa-times"></i> Program Full
                                                </button>
                                            <?php else: ?>
                                                <form method="post" action="enroll_program.php">
                                                    <input type="hidden" name="program_id" value="<?php echo $program['program_id']; ?>">
                                                    <button type="submit" class="btn btn-primary btn-block">
                                                        <i class="fas fa-plus"></i> Enroll Now
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No programs are currently available. Please check back later.
                                </div>
                            </div>
                        <?php endif; ?>
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