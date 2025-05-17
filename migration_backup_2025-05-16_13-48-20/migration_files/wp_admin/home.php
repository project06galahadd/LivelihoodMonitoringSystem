<?php
include "includes/header.php";
include "includes/navbar.php";
include "includes/sidebar.php";

// Function to count records
function getCount($conn, $table, $condition = '1')
{
  $sql = "SELECT COUNT(*) AS total FROM $table WHERE $condition";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  return $row['total'];
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .dashboard-title {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #3498db;
  }

  .small-box {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
  }

  .small-box:hover {
    transform: translateY(-5px);
  }

  .small-box .inner {
    padding: 20px;
  }

  .small-box h3 {
    font-size: 38px;
    font-weight: bold;
    margin: 0;
    white-space: nowrap;
    padding: 0;
  }

  .small-box p {
    font-size: 15px;
    margin-bottom: 0;
  }

  .card {
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
  }

  .card-header {
    background-color: #fff;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    padding: 15px;
  }

  .card-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
  }

  .table {
    margin-bottom: 0;
  }

  .table thead th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    color: #2c3e50;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
  }

  .table td {
    vertical-align: middle;
    font-size: 14px;
  }

  .status-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 500;
  }

  .status-pending {
    background-color: #ffeeba;
    color: #856404;
  }

  .status-approved {
    background-color: #d4edda;
    color: #155724;
  }

  .status-archived {
    background-color: #f8d7da;
    color: #721c24;
  }

  .status-active {
    background-color: #cce5ff;
    color: #004085;
  }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">DASHBOARD</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">HOME</a></li>
            <li class="breadcrumb-item active">DASHBOARD</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-info">
            <div class="inner">
              <h3><?= getCount($conn, 'tbl_members'); ?></h3>
              <p>Total Members</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
            <a href="member.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-success">
            <div class="inner">
              <h3><?= getCount($conn, 'tbl_barangay'); ?></h3>
              <p>Active Barangays</p>
            </div>
            <div class="icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <a href="lgu.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-purple">
            <div class="inner">
              <h3><?= getCount($conn, 'tbl_members', "GENDER='FEMALE'"); ?></h3>
              <p>Female Members</p>
            </div>
            <div class="icon">
              <i class="fas fa-female"></i>
            </div>
            <a href="member.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-danger">
            <div class="inner">
              <h3><?= getCount($conn, 'tbl_members', "GENDER='MALE'"); ?></h3>
              <p>Male Members</p>
            </div>
            <div class="icon">
              <i class="fas fa-male"></i>
            </div>
            <a href="member.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Tables for List of Beneficiaries and Total of Beneficiaries -->
      <div class="row">
        <!-- List of Beneficiaries Table -->
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-users mr-2"></i>
                Recent Beneficiaries
              </h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Barangay</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Record #</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $beneficiaries_query = "SELECT * FROM tbl_members ORDER BY RECORD_NUMBER ASC LIMIT 10";
                  $result_beneficiaries = $conn->query($beneficiaries_query);

                  // Function to calculate age based on DATE_OF_BIRTH
                  function calculateAge($dob)
                  {
                    $birthdate = new DateTime($dob);
                    $currentDate = new DateTime();
                    $interval = $currentDate->diff($birthdate);
                    return $interval->y; // Return age in years
                  }

                  // Display the beneficiaries data
                  while ($beneficiary = $result_beneficiaries->fetch_assoc()) {
                    $statusClass = '';
                    switch ($beneficiary['STATUS']) {
                      case 'PENDING':
                        $statusClass = 'status-pending';
                        break;
                      case 'APPROVED':
                        $statusClass = 'status-approved';
                        break;
                      case 'ACTIVE':
                        $statusClass = 'status-active';
                        break;
                      case 'ARCHIVED':
                        $statusClass = 'status-archived';
                        break;
                    }

                    echo "<tr>";
                    echo "<td>" . $beneficiary['MEMID'] . "</td>";
                    echo "<td><strong>" . $beneficiary['FIRSTNAME'] . " " . $beneficiary['LASTNAME'] . "</strong></td>";
                    echo "<td>" . $beneficiary['BARANGAY'] . "</td>";
                    echo "<td>" . calculateAge($beneficiary['DATE_OF_BIRTH']) . "</td>";
                    echo "<td>" . $beneficiary['GENDER'] . "</td>";
                    echo "<td>" . $beneficiary['RECORD_NUMBER'] . "</td>";
                    echo "<td><span class='status-badge " . $statusClass . "'>" . $beneficiary['STATUS'] . "</span></td>";
                    echo "</tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-chart-pie mr-2"></i>
                Member Status Distribution
              </h3>
            </div>
            <div class="card-body">
              <canvas id="statusPieChart"></canvas>
            </div>
          </div>

          <!-- Location Map Section -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="text-red fa fa-regular fa-location-dot fa-fade"></i> LOCATION MAP</h3>
            </div>
            <div class="card-body p-0">
              <div class="col-12">
                <div class="embed-responsive" style="height: 400px;">
                  <iframe class="embed-responsive-item" src="https://maps.google.com/maps?q=<?= $SYS_ADDRESS; ?>&t=&z=15&ie=UTF8&iwloc=&output=embed" allowfullscreen></iframe>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total of Beneficiaries Table -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-calculator mr-2"></i>
                Beneficiary Statistics
              </h3>
            </div>
            <div class="card-body p-0">
              <table class="table table-striped">
                <tbody>
                  <tr>
                    <td><i class="fas fa-clock text-warning mr-2"></i>Pending</td>
                    <td><span class="badge badge-warning"><?= getCount($conn, 'tbl_members', "STATUS='PENDING'"); ?></span></td>
                  </tr>
                  <tr>
                    <td><i class="fas fa-check text-success mr-2"></i>Approved</td>
                    <td><span class="badge badge-success"><?= getCount($conn, 'tbl_members', "STATUS='APPROVED'"); ?></span></td>
                  </tr>
                  <tr>
                    <td><i class="fas fa-check-circle text-primary mr-2"></i>Active</td>
                    <td><span class="badge badge-primary"><?= getCount($conn, 'tbl_members', "STATUS='ACTIVE'"); ?></span></td>
                  </tr>
                  <tr>
                    <td><i class="fas fa-archive text-danger mr-2"></i>Archived</td>
                    <td><span class="badge badge-danger"><?= getCount($conn, 'tbl_members', "STATUS='ARCHIVED'"); ?></span></td>
                  </tr>
                  <tr>
                    <td><strong><i class="fas fa-users mr-2"></i>Total Members</strong></td>
                    <td><span class="badge badge-primary"><?= getCount($conn, 'tbl_members'); ?></span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Barangay Record Table -->
        <div class="col-md-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-map-marked-alt mr-2"></i>
                Barangay Member Distribution
              </h3>
            </div>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Barangay Name</th>
                    <th>Total Members</th>
                    <th>Distribution</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $barangay_query = "SELECT BARANGAY, COUNT(*) AS total FROM tbl_members GROUP BY BARANGAY ORDER BY total DESC";
                  $barangay_result = $conn->query($barangay_query);
                  $total_members = getCount($conn, 'tbl_members');

                  $count = 1;
                  while ($row = $barangay_result->fetch_assoc()) {
                    $percentage = ($total_members > 0) ? round(($row['total'] / $total_members) * 100) : 0;
                    echo "<tr>";
                    echo "<td>" . $count++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['BARANGAY']) . "</td>";
                    echo "<td>" . $row['total'] . "</td>";
                    echo "<td>
                            <div class='progress progress-sm'>
                              <div class='progress-bar bg-primary' role='progressbar' style='width: " . $percentage . "%'></div>
                            </div>
                            <small class='text-muted'>" . $percentage . "%</small>
                          </td>";
                    echo "</tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <script>
          // Update the pie chart
          const ctx = document.getElementById('statusPieChart').getContext('2d');
          new Chart(ctx, {
            type: 'doughnut',
            data: {
              labels: ['Pending', 'Approved', 'Active', 'Archived'],
              datasets: [{
                data: [
                  <?= getCount($conn, 'tbl_members', "STATUS='PENDING'"); ?>,
                  <?= getCount($conn, 'tbl_members', "STATUS='APPROVED'"); ?>,
                  <?= getCount($conn, 'tbl_members', "STATUS='ACTIVE'"); ?>,
                  <?= getCount($conn, 'tbl_members', "STATUS='ARCHIVED'"); ?>
                ],
                backgroundColor: [
                  '#ffc107',
                  '#28a745',
                  '#007bff',
                  '#dc3545'
                ],
                borderWidth: 0
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: 'bottom'
                }
              },
              cutout: '70%'
            }
          });
        </script>
        <!-- Alert messages -->
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-warning"></i> ERROR!</h4>
            <?= $_SESSION['error'];
            unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-check"></i> SUCCESS!</h4>
            <?= $_SESSION['success'];
            unset($_SESSION['success']); ?>
          </div>
        <?php endif; ?>

      </div> <!-- /.container-fluid -->
  </section> <!-- /.content -->

</div> <!-- /.content-wrapper -->

<?php
// Fetch schedule list
$schedules = $conn->query("SELECT * FROM `schedule_list`");
$sched_res = [];
foreach ($schedules->fetch_all(MYSQLI_ASSOC) as $row) {
  $row['sdate'] = date("F d, Y H:i A", strtotime($row['start_datetime']));
  $row['edate'] = date("F d, Y H:i A", strtotime($row['end_datetime']));
  $sched_res[$row['id']] = $row;
}

// Close DB connection
if (isset($conn)) $conn->close();
?>

<?php include "includes/footer.php"; ?>

</div> <!-- /.wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="../plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI -->
<script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<!-- ChartJS -->
<script src="../plugins/chart.js/Chart.min.js"></script>
<!-- Page specific script -->
<script>
  $(function() {
    // Initialize sidebar toggle
    $('[data-widget="pushmenu"]').PushMenu('collapse');

    // Initialize dropdowns
    $('.dropdown-toggle').dropdown();

    // Initialize treeview
    $('[data-widget="treeview"]').Treeview('init');

    // Initialize navbar dropdown
    $('.nav-item.dropdown').on('show.bs.dropdown', function() {
      $(this).find('.dropdown-menu').first().stop(true, true).slideDown(200);
    });

    $('.nav-item.dropdown').on('hide.bs.dropdown', function() {
      $(this).find('.dropdown-menu').first().stop(true, true).slideUp(200);
    });

    // Initialize sidebar menu
    $('.nav-sidebar .nav-item').on('click', function() {
      if ($(this).hasClass('has-treeview')) {
        $(this).toggleClass('menu-open');
      }
    });

    // Initialize sidebar collapse
    $('.sidebar-toggle').on('click', function() {
      $('body').toggleClass('sidebar-collapse');
    });
  });
</script>
</body>

</html>