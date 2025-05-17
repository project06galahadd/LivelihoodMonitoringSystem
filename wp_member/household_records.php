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
    'household_size' => $_POST['household_size'],
    'monthly_income' => $_POST['monthly_income'],
    'main_source_income' => $_POST['main_source_income'],
    'land_ownership' => $_POST['land_ownership'],
    'land_area' => $_POST['land_area'],
    'land_type' => $_POST['land_type'],
    'house_type' => $_POST['house_type'],
    'house_materials' => $_POST['house_materials'],
    'water_source' => $_POST['water_source'],
    'toilet_facility' => $_POST['toilet_facility'],
    'electricity' => $_POST['electricity'],
    'garbage_disposal' => $_POST['garbage_disposal']
  ];

  // Insert into database
  $sql = "INSERT INTO household_records (
    interviewed_by_lastname, interviewed_by_firstname, interviewed_by_middlename, interviewed_by_position,
    beneficiary_lastname, beneficiary_firstname, beneficiary_middlename, beneficiary_relationship,
    beneficiary_age, beneficiary_birth_date, beneficiary_marital_status, beneficiary_educational_attainment,
    beneficiary_occupation, family_lastname, family_firstname, family_middlename, family_age,
    family_birth_date, family_birth_place, family_sex, complete_address, sitio_purok, barangay,
    town, province, household_size, monthly_income, main_source_income, land_ownership,
    land_area, land_type, house_type, house_materials, water_source, toilet_facility,
    electricity, garbage_disposal, member_id
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssssssssssssssssssssssssssssssssssssssssssss",
    $interviewed_by['lastname'], $interviewed_by['firstname'], $interviewed_by['middlename'], $interviewed_by['position'],
    $beneficiary['lastname'], $beneficiary['firstname'], $beneficiary['middlename'], $beneficiary['relationship'],
    $beneficiary['age'], $beneficiary['birth_date'], $beneficiary['marital_status'], $beneficiary['educational_attainment'],
    $beneficiary['occupation'], $family['lastname'], $family['firstname'], $family['middlename'], $family['age'],
    $family['birth_date'], $family['birth_place'], $family['sex'], $address['complete'], $address['sitio_purok'], $address['barangay'],
    $address['town'], $address['province'], $other_info['household_size'], $other_info['monthly_income'], $other_info['main_source_income'], $other_info['land_ownership'],
    $other_info['land_area'], $other_info['land_type'], $other_info['house_type'], $other_info['house_materials'], $other_info['water_source'], $other_info['toilet_facility'],
    $other_info['electricity'], $other_info['garbage_disposal'], $_SESSION['user_id']
  );

  if ($stmt->execute()) {
    $_SESSION['success'] = "Household record added successfully";
  } else {
    $_SESSION['error'] = "Error adding household record";
  }

  header('location: household_records.php');
  exit();
}

// Get all household records for the current member
$sql = "SELECT * FROM household_records WHERE member_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Household Records | MSWD</title>
    <link rel="icon" type="image/png" href="/LivelihoodMonitoringSystem/dist/img/Logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
        }
        
        body { font-family: 'Source Sans Pro', sans-serif; background: var(--light-bg); }
        .content-header h1 { color: var(--text-color); font-weight: 600; font-size: 1.8rem; }
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); background: #fff; }
        .card-header { background: #fff; border-bottom: 1px solid #eee; border-radius: 10px 10px 0 0; }
        .card-title { color: var(--text-color); font-weight: 600; }
        .table thead th { background: #f8f9fa; }
        .btn-primary { background: var(--secondary-color); border: none; }
        .btn-primary:hover { background: #217dbb; }
        
        /* Add hover effects */
        .nav-sidebar .nav-item.hovered {
            background: rgba(255,255,255,0.05);
        }
        
        /* Fix for active state */
        .nav-sidebar .nav-link.active {
            background: #2c3e50;
            color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .nav-sidebar .nav-link.active i {
            color: #fff;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Household Records</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                                <li class="breadcrumb-item active">Household Records</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
            --light-bg: #f8f9fa;
        }
        body { font-family: 'Source Sans Pro', sans-serif; background: var(--light-bg); }
        .content-header h1 { color: var(--text-color); font-weight: 600; font-size: 1.8rem; }
        .card { border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); background: #fff; }
        .card-header { background: #fff; border-bottom: 1px solid #eee; border-radius: 10px 10px 0 0; }
        .card-title { color: var(--text-color); font-weight: 600; }
        .table thead th { background: #f8f9fa; }
        .btn-primary { background: var(--secondary-color); border: none; }
        .btn-primary:hover { background: #217dbb; }
    </style>
</head>
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
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i> Success!</h5>
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-ban"></i> Error!</h5>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
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
        </section>
    </div>
    <?php include "includes/footer.php"; ?>
</div>

<!-- JavaScript Includes -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
<script src="../js/sidebar.js"></script>
</body>
</html>

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