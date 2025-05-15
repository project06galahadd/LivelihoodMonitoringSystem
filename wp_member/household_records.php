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
  // Get form data
  $interviewed_by = [
    'lastname' => $_POST['interviewed_lastname'],
    'firstname' => $_POST['interviewed_firstname'],
    'middlename' => $_POST['interviewed_middlename'],
    'position' => $_POST['interviewed_position']
  ];

  $beneficiary = [
    'lastname' => $_POST['beneficiary_lastname'],
    'firstname' => $_POST['beneficiary_firstname'],
    'middlename' => $_POST['beneficiary_middlename'],
    'relationship' => $_POST['beneficiary_relationship'],
    'age' => $_POST['age'],
    'birth_date' => $_POST['birth_date'],
    'marital_status' => $_POST['marital_status'],
    'educational_attainment' => $_POST['educational_attainment'],
    'occupation' => $_POST['occupation']
  ];

  $family = [
    'lastname' => $_POST['family_lastname'],
    'firstname' => $_POST['family_firstname'],
    'middlename' => $_POST['family_middlename'],
    'age' => $_POST['family_age'],
    'birth_date' => $_POST['family_birth_date'],
    'birth_place' => $_POST['family_birth_place'],
    'sex' => $_POST['family_sex']
  ];

  $address = [
    'complete' => $_POST['complete_address'],
    'sitio_purok' => $_POST['sitio_purok'],
    'barangay' => $_POST['barangay'],
    'town' => $_POST['town'],
    'province' => $_POST['province']
  ];

  $other_info = [
    'marital_status' => $_POST['marital_status_family'],
    'religion' => $_POST['religion'],
    'sector' => $_POST['sector'],
    'educational_attainment' => $_POST['educational_attainment_family'],
    'occupation' => $_POST['occupation_family'],
    'monthly_income' => $_POST['estimated_monthly_income'],
    'contact_number' => $_POST['contact_number']
  ];

  $problem = $_POST['problem_presented'];
  $member_id = $_SESSION['user_id'];

  // Start transaction
  $conn->begin_transaction();

  try {
    // Insert main record
    $sql = "INSERT INTO tbl_household_case_records (
            interviewed_by_lastname, interviewed_by_firstname, interviewed_by_middlename, interviewed_by_position,
            beneficiary_lastname, beneficiary_firstname, beneficiary_middlename, beneficiary_relationship,
            age, birth_date, marital_status, educational_attainment, occupation,
            family_lastname, family_firstname, family_middlename, family_age, family_birth_date,
            family_birth_place, family_sex, complete_address, sitio_purok, barangay, town, province,
            marital_status_family, religion, sector, educational_attainment_family, occupation_family,
            estimated_monthly_income, contact_number, problem_presented, submitted_by, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
      "sssssssssssssssssssssssssssssssssi",
      $interviewed_by['lastname'],
      $interviewed_by['firstname'],
      $interviewed_by['middlename'],
      $interviewed_by['position'],
      $beneficiary['lastname'],
      $beneficiary['firstname'],
      $beneficiary['middlename'],
      $beneficiary['relationship'],
      $beneficiary['age'],
      $beneficiary['birth_date'],
      $beneficiary['marital_status'],
      $beneficiary['educational_attainment'],
      $beneficiary['occupation'],
      $family['lastname'],
      $family['firstname'],
      $family['middlename'],
      $family['age'],
      $family['birth_date'],
      $family['birth_place'],
      $family['sex'],
      $address['complete'],
      $address['sitio_purok'],
      $address['barangay'],
      $address['town'],
      $address['province'],
      $other_info['marital_status'],
      $other_info['religion'],
      $other_info['sector'],
      $other_info['educational_attainment'],
      $other_info['occupation'],
      $other_info['monthly_income'],
      $other_info['contact_number'],
      $problem,
      $member_id
    );

    $stmt->execute();
    $household_id = $conn->insert_id;

    // Insert family composition
    if (isset($_POST['family_member_name']) && is_array($_POST['family_member_name'])) {
      $sql_family = "INSERT INTO tbl_family_composition (
                household_case_record_id, name, age, marital_status, relationship,
                educational_attainment, occupation, monthly_income
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

      $stmt_family = $conn->prepare($sql_family);

      foreach ($_POST['family_member_name'] as $key => $name) {
        if (!empty($name)) {
          $stmt_family->bind_param(
            "issssssd",
            $household_id,
            $name,
            $_POST['family_member_age'][$key],
            $_POST['family_member_marital_status'][$key],
            $_POST['family_member_relationship'][$key],
            $_POST['family_member_education'][$key],
            $_POST['family_member_occupation'][$key],
            $_POST['family_member_income'][$key]
          );
          $stmt_family->execute();
        }
      }
    }

    $conn->commit();
    $_SESSION['success'] = "Household case record submitted successfully";
  } catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error submitting household case record: " . $e->getMessage();
  }

  header('location: household_records.php');
  exit();
}

// Get all household records for the current member
$sql = "SELECT * FROM tbl_household_case_records WHERE submitted_by = ? ORDER BY date_created DESC";
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
              <h1 class="m-0">Household Case Records</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                <li class="breadcrumb-item active">Household Case Records</li>
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
            <div class="col-12">
              <div class="card">
                <div class="card-header bg-primary">
                  <h3 class="card-title text-white">
                    <i class="fas fa-file-alt mr-2"></i>
                    Submit New Household Case Record
                  </h3>
                </div>
                <div class="card-body">
                  <form action="" method="post" class="needs-validation" novalidate>
                    <!-- Interviewed By Section -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h4 class="mb-0">
                          <i class="fas fa-user-tie text-primary mr-2"></i>
                          Interviewed By
                        </h4>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-user text-primary mr-1"></i> Last Name</label>
                              <input type="text" class="form-control" name="interviewed_lastname" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-user text-primary mr-1"></i> First Name</label>
                              <input type="text" class="form-control" name="interviewed_firstname" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-user text-primary mr-1"></i> Middle Name</label>
                              <input type="text" class="form-control" name="interviewed_middlename">
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-id-badge text-primary mr-1"></i> Position</label>
                              <input type="text" class="form-control" name="interviewed_position" required>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Beneficiary Section -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h4 class="mb-0">
                          <i class="fas fa-user-circle text-primary mr-2"></i>
                          Beneficiary Information
                        </h4>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-user text-primary mr-1"></i> Last Name</label>
                              <input type="text" class="form-control" name="beneficiary_lastname" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-user text-primary mr-1"></i> First Name</label>
                              <input type="text" class="form-control" name="beneficiary_firstname" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-user text-primary mr-1"></i> Middle Name</label>
                              <input type="text" class="form-control" name="beneficiary_middlename">
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-link text-primary mr-1"></i> Relationship</label>
                              <input type="text" class="form-control" name="beneficiary_relationship" required>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label><i class="fas fa-birthday-cake text-primary mr-1"></i> Age</label>
                              <input type="number" class="form-control" name="age" required>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label><i class="fas fa-calendar text-primary mr-1"></i> Birth Date</label>
                              <input type="date" class="form-control" name="birth_date" required>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label><i class="fas fa-ring text-primary mr-1"></i> Marital Status</label>
                              <select class="form-control" name="marital_status" required>
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Separated">Separated</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-graduation-cap text-primary mr-1"></i> Educational Attainment</label>
                              <input type="text" class="form-control" name="educational_attainment" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-briefcase text-primary mr-1"></i> Occupation</label>
                              <input type="text" class="form-control" name="occupation" required>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Family Profile Section -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h4 class="mb-0">
                          <i class="fas fa-users text-primary mr-2"></i>
                          Family Profile
                        </h4>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-user text-primary mr-1"></i> Last Name</label>
                              <input type="text" class="form-control" name="family_lastname" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-user text-primary mr-1"></i> First Name</label>
                              <input type="text" class="form-control" name="family_firstname" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-user text-primary mr-1"></i> Middle Name</label>
                              <input type="text" class="form-control" name="family_middlename">
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-birthday-cake text-primary mr-1"></i> Age</label>
                              <input type="number" class="form-control" name="family_age" required>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label><i class="fas fa-calendar text-primary mr-1"></i> Birth Date</label>
                              <input type="date" class="form-control" name="family_birth_date" required>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label><i class="fas fa-map-marker-alt text-primary mr-1"></i> Birth Place</label>
                              <input type="text" class="form-control" name="family_birth_place" required>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label><i class="fas fa-venus-mars text-primary mr-1"></i> Sex</label>
                              <select class="form-control" name="family_sex" required>
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Address Section -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h4 class="mb-0">
                          <i class="fas fa-map-marked-alt text-primary mr-2"></i>
                          Complete Address
                        </h4>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label><i class="fas fa-home text-primary mr-1"></i> Complete Address</label>
                              <textarea class="form-control" name="complete_address" rows="2" required></textarea>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-map text-primary mr-1"></i> Sitio/Purok</label>
                              <input type="text" class="form-control" name="sitio_purok" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-map text-primary mr-1"></i> Barangay</label>
                              <input type="text" class="form-control" name="barangay" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-city text-primary mr-1"></i> Town</label>
                              <input type="text" class="form-control" name="town" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-flag text-primary mr-1"></i> Province</label>
                              <input type="text" class="form-control" name="province" required>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Other Information -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h4 class="mb-0">
                          <i class="fas fa-info-circle text-primary mr-2"></i>
                          Other Information
                        </h4>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-ring text-primary mr-1"></i> Marital Status</label>
                              <select class="form-control" name="marital_status_family" required>
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Separated">Separated</option>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-church text-primary mr-1"></i> Religion</label>
                              <input type="text" class="form-control" name="religion" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-users text-primary mr-1"></i> Sector</label>
                              <input type="text" class="form-control" name="sector" required>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label><i class="fas fa-graduation-cap text-primary mr-1"></i> Educational Attainment</label>
                              <input type="text" class="form-control" name="educational_attainment_family" required>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label><i class="fas fa-briefcase text-primary mr-1"></i> Occupation</label>
                              <input type="text" class="form-control" name="occupation_family" required>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label><i class="fas fa-money-bill-wave text-primary mr-1"></i> Estimated Monthly Income</label>
                              <input type="number" step="0.01" class="form-control" name="estimated_monthly_income" required>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label><i class="fas fa-phone text-primary mr-1"></i> Contact Number</label>
                              <input type="text" class="form-control" name="contact_number" required>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Problem Presented -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h4 class="mb-0">
                          <i class="fas fa-exclamation-circle text-primary mr-2"></i>
                          Problem Presented
                        </h4>
                      </div>
                      <div class="card-body">
                        <div class="form-group">
                          <textarea class="form-control" name="problem_presented" rows="4" required></textarea>
                        </div>
                      </div>
                    </div>

                    <!-- Family Composition Section -->
                    <div class="card mb-4">
                      <div class="card-header bg-light">
                        <h4 class="mb-0">
                          <i class="fas fa-users-cog text-primary mr-2"></i>
                          Family Composition
                        </h4>
                      </div>
                      <div class="card-body">
                        <div class="table-responsive">
                          <table class="table table-bordered table-hover" id="familyCompositionTable">
                            <thead class="bg-light">
                              <tr>
                                <th><i class="fas fa-user text-primary mr-1"></i> Name</th>
                                <th><i class="fas fa-birthday-cake text-primary mr-1"></i> Age</th>
                                <th><i class="fas fa-ring text-primary mr-1"></i> Marital Status</th>
                                <th><i class="fas fa-link text-primary mr-1"></i> Relationship</th>
                                <th><i class="fas fa-graduation-cap text-primary mr-1"></i> Educational Attainment</th>
                                <th><i class="fas fa-briefcase text-primary mr-1"></i> Occupation</th>
                                <th><i class="fas fa-money-bill-wave text-primary mr-1"></i> Monthly Income</th>
                                <th><i class="fas fa-cog text-primary mr-1"></i> Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td><input type="text" class="form-control" name="family_member_name[]" required></td>
                                <td><input type="number" class="form-control" name="family_member_age[]" required></td>
                                <td>
                                  <select class="form-control" name="family_member_marital_status[]" required>
                                    <option value="">Select Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Separated">Separated</option>
                                  </select>
                                </td>
                                <td><input type="text" class="form-control" name="family_member_relationship[]" required></td>
                                <td><input type="text" class="form-control" name="family_member_education[]" required></td>
                                <td><input type="text" class="form-control" name="family_member_occupation[]" required></td>
                                <td><input type="number" step="0.01" class="form-control" name="family_member_income[]" required></td>
                                <td>
                                  <button type="button" class="btn btn-danger btn-sm remove-family-member">
                                    <i class="fas fa-times"></i>
                                  </button>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                          <button type="button" class="btn btn-success btn-sm" id="addFamilyMember">
                            <i class="fas fa-plus"></i> Add Family Member
                          </button>
                        </div>
                      </div>
                    </div>

                    <div class="row mt-4">
                      <div class="col-12">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg">
                          <i class="fas fa-save mr-2"></i>Submit Record
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Records List -->
            <div class="col-12 mt-4">
              <div class="card">
                <div class="card-header bg-primary">
                  <h3 class="card-title text-white">
                    <i class="fas fa-list mr-2"></i>
                    My Household Case Records
                  </h3>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                      <thead class="bg-light">
                        <tr>
                          <th><i class="fas fa-calendar text-primary mr-1"></i> Date Submitted</th>
                          <th><i class="fas fa-user text-primary mr-1"></i> Beneficiary Name</th>
                          <th><i class="fas fa-map-marker-alt text-primary mr-1"></i> Address</th>
                          <th><i class="fas fa-exclamation-circle text-primary mr-1"></i> Problem Presented</th>
                          <th><i class="fas fa-tag text-primary mr-1"></i> Status</th>
                          <th><i class="fas fa-comment text-primary mr-1"></i> Remarks</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                            $status_class = '';
                            switch ($row['status']) {
                              case 'PENDING':
                                $status_class = 'warning';
                                break;
                              case 'APPROVED':
                                $status_class = 'success';
                                break;
                              case 'REJECTED':
                                $status_class = 'danger';
                                break;
                            }

                            echo "<tr>";
                            echo "<td>" . date('M d, Y', strtotime($row['date_created'])) . "</td>";
                            echo "<td>" . $row['beneficiary_lastname'] . ", " . $row['beneficiary_firstname'] . " " . $row['beneficiary_middlename'] . "</td>";
                            echo "<td>" . $row['complete_address'] . "</td>";
                            echo "<td>" . $row['problem_presented'] . "</td>";
                            echo "<td><span class='badge badge-" . $status_class . "'>" . $row['status'] . "</span></td>";
                            echo "<td>" . ($row['remarks'] ? $row['remarks'] : 'No remarks') . "</td>";
                            echo "</tr>";
                          }
                        } else {
                          echo "<tr><td colspan='6' class='text-center'>No household case records found</td></tr>";
                        }
                        ?>
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

    <?php include "includes/footer.php"; ?>
  </div>

  <script src="../plugins/jquery/jquery.min.js"></script>
  <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../dist/js/adminlte.min.js"></script>
  <script>
    // Form validation
    (function() {
      'use strict';
      window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();

    $(document).ready(function() {
      // Add new family member row
      $('#addFamilyMember').click(function() {
        var newRow = `
                <tr>
                    <td><input type="text" class="form-control" name="family_member_name[]" required></td>
                    <td><input type="number" class="form-control" name="family_member_age[]" required></td>
                    <td>
                        <select class="form-control" name="family_member_marital_status[]" required>
                            <option value="">Select Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Separated">Separated</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control" name="family_member_relationship[]" required></td>
                    <td><input type="text" class="form-control" name="family_member_education[]" required></td>
                    <td><input type="text" class="form-control" name="family_member_occupation[]" required></td>
                    <td><input type="number" step="0.01" class="form-control" name="family_member_income[]" required></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-family-member"><i class="fas fa-times"></i></button>
                    </td>
                </tr>
            `;
        $('#familyCompositionTable tbody').append(newRow);
      });

      // Remove family member row
      $(document).on('click', '.remove-family-member', function() {
        if ($('#familyCompositionTable tbody tr').length > 1) {
          $(this).closest('tr').remove();
        } else {
          alert('At least one family member is required.');
        }
      });
    });
  </script>
</body>

</html>