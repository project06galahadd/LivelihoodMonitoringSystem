<?php
session_start();
include "includes/conn.php";
include "includes/session.php";

// Get user data
$sql = "SELECT * FROM tbl_users WHERE ID = '".$_SESSION['admin']."'";
$query = $conn->query($sql);
$user = $query->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Profile</title>
  <?php include 'includes/header.php'; ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <?php include 'includes/navbar.php'; ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php include 'includes/sidebar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profile</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Profile</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                  <?php if(!empty($user['PROFILE'])): ?>
                    <img class="profile-user-img img-fluid img-circle" src="data:image/jpeg;base64,<?php echo base64_encode($user['PROFILE']); ?>" alt="User profile picture">
                  <?php else: ?>
                    <img class="profile-user-img img-fluid img-circle" src="../dist/img/user4-128x128.jpg" alt="User profile picture">
                  <?php endif; ?>
                </div>

                <h3 class="profile-username text-center"><?php echo $user['FIRSTNAME'].' '.$user['LASTNAME']; ?></h3>

                <p class="text-muted text-center"><?php echo $user['ROLE']; ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Username</b> <a class="float-right"><?php echo $user['USERNAME']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Contact</b> <a class="float-right"><?php echo $user['CONTACT']; ?></a>
                  </li>
                </ul>

                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#profile">
                  <i class="fa fa-camera"></i> Change Photo
                </button>
                <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#editProfile">
                  <i class="fa fa-edit"></i> Edit Profile
                </button>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/profile_modal.php'; ?>

  <script>
    function preview() {
      frame.src = URL.createObjectURL(event.target.files[0]);
    }
  </script>
</body>
</html> 