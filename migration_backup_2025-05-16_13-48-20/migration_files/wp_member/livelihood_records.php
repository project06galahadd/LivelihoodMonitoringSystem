<?php
session_start();
include "../includes/conn.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
    $livelihood_type = $_POST['livelihood_type'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $status = $_POST['status'];
    $member_id = $_SESSION['user_id'];

    $sql = "INSERT INTO tbl_livelihood (livelihood_type, description, start_date, status, member_id) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $livelihood_type, $description, $start_date, $status, $member_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Livelihood record added successfully";
    } else {
        $_SESSION['error'] = "Error adding livelihood record";
    }
    header('location: livelihood_records.php');
    exit();
}

// Get all livelihood records for the current member
$sql = "SELECT * FROM tbl_livelihood WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include "includes/header.php"; ?>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php include "includes/navbar.php"; ?>
    <?php include "includes/sidebar.php"; ?>

    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Livelihood Records</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active">Livelihood Records</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <section class="content">
        <div class="container-fluid">
          <?php
          if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                    ' . $_SESSION['success'] . '
                  </div>';
            unset($_SESSION['success']);
          }
          if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-ban"></i> Error!</h5>
                    ' . $_SESSION['error'] . '
                  </div>';
            unset($_SESSION['error']);
          }
          ?>

          <div class="row">
            <div class="col-md-4">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Add New Livelihood</h3>
                </div>
                <div class="card-body">
                  <form action="" method="post">
                    <div class="form-group">
                      <label for="livelihood_type">Livelihood Type</label>
                      <select class="form-control" id="livelihood_type" name="livelihood_type" required>
                        <option value="">Select Type</option>
                        <option value="Farming">Farming</option>
                        <option value="Fishing">Fishing</option>
                        <option value="Small Business">Small Business</option>
                        <option value="Handicraft">Handicraft</option>
                        <option value="Livestock">Livestock</option>
                        <option value="Other">Other</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="description">Description</label>
                      <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                      <label for="start_date">Start Date</label>
                      <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="form-group">
                      <label for="status">Status</label>
                      <select class="form-control" id="status" name="status" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                        <option value="Planning">Planning</option>
                      </select>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                  </form>
                </div>
              </div>
            </div>

            <div class="col-md-8">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Livelihood Records List</h3>
                </div>
                <div class="card-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Livelihood Type</th>
                        <th>Description</th>
                        <th>Start Date</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td>" . $row['livelihood_type'] . "</td>";
                          echo "<td>" . $row['description'] . "</td>";
                          echo "<td>" . date('M d, Y', strtotime($row['start_date'])) . "</td>";
                          echo "<td><span class='badge badge-" . ($row['status'] == 'Active' ? 'success' : ($row['status'] == 'Planning' ? 'info' : 'warning')) . "'>" . $row['status'] . "</span></td>";
                          echo "</tr>";
                        }
                      } else {
                        echo "<tr><td colspan='4' class='text-center'>No livelihood records found</td></tr>";
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

    <?php include "includes/footer.php"; ?>
  </div>

  <script src="../plugins/jquery/jquery.min.js"></script>
  <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../dist/js/adminlte.min.js"></script>
</body>
</html> 