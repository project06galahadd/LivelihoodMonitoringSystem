<?php @include "includes/header.php";
if (!empty($_GET['member_info'])) {
  $sql = "SELECT * FROM tbl_members WHERE MEMID= '" . $_GET['member_info'] . "'";
  $query = $conn->query($sql);
  if ($query->num_rows > 0) {
    $row = $query->fetch_assoc();
    $ID               = $row['MEMID'];
    $RECORD_NUMBER    = $row['RECORD_NUMBER'];
    $MEMBERS          = $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME'];
    $GENDER           = $row['GENDER'];
    $DATE_OF_BIRTH    = $row['DATE_OF_BIRTH'];
    $AGE              = $row['AGE'];
    $MOBILE           = $row['MOBILE'];
    $BARANGAY         = $row['BARANGAY'];
    $ADDRESS          = $row['ADDRESS'];
    $EDUCATIONAL_BACKGROUND          = $row['EDUCATIONAL_BACKGROUND'];
    $EMPLOYMENT_HISTORY             = $row['EMPLOYMENT_HISTORY'];
    $SKILLS_QUALIFICATION           = $row['SKILLS_QUALIFICATION'];
    $DESIRED_LIVELIHOOD_PROGRAM     = $row['DESIRED_LIVELIHOOD_PROGRAM'];
    $EXP_LIVELIHOOD_PROGRAM_CHOSEN  = $row['EXP_LIVELIHOOD_PROGRAM_CHOSEN'];
    $CURRENT_LIVELIHOOD_SITUATION   = $row['CURRENT_LIVELIHOOD_SITUATION'];
    $REQUIRED_TRAINING              = $row['REQUIRED_TRAINING'];
    $REASON_INTERESTED_IN_LIVELIHOOD = $row['REASON_INTERESTED_IN_LIVELIHOOD'];
    $VALID_ID_NUMBER                = $row['VALID_ID_NUMBER'];
    $UPLOAD_ID                      = $row['UPLOAD_ID'];
    $UPLOAD_WITH_SELFIE             = $row['UPLOAD_WITH_SELFIE'];
    $DATE_OF_APPLICATION            = $row['DATE_OF_APPLICATION'];
    $PROFILE                        = $row['PROFILE'];
  } else {
    header("location:member.php?members");
  }
}


?>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- .navbar -->
    <?php @include "includes/navbar.php"; ?>
    <!-- /.navbar -->
    <?php @include "includes/sidebar.php"; ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>MEMBER PROFILE</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">HOME</a></li>
                <li class="breadcrumb-item active">PROFILE</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <?php
          if (isset($_SESSION['error'])) {
            echo "
					<div id='alert' class='alert alert-danger' id='alert'>
					  <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
					  <h4><i class='icon fa fa-warning'></i> ERROR!</h4>
					  " . $_SESSION['error'] . "
					</div>
				  ";
            unset($_SESSION['error']);
          }
          if (isset($_SESSION['success'])) {
            echo "
					<div id='alert' class='alert alert-success' id='alert'>
					  <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
					  <h4><i class='icon fa fa-check'></i> SUCCESS!</h4>
					  " . $_SESSION['success'] . "
					</div>
				  ";
            unset($_SESSION['success']);
          }
          ?>
          <div class="row">
            <div class="col-md-3">
              <!-- Profile Image -->
              <div class="card card-secondarys card-outline">
                <div class="card-body box-profile">
                  <div class="text-center">
                    <?php
                    if ($PROFILE == "") {
                    ?>
                      <img width="250" height="250" class="img-thumbnail" src="../dist/img/profile.jpg" alt="User profile picture">
                    <?php } else { ?>
                      <img src="data:image/jpg;charset=utf8;base64,<?= base64_encode($PROFILE); ?>" width="250" height="250" class="img-thumbnail">
                    <?php } ?>

                  </div>
                  <p class="text-muted text-center"><a href="#baptised" data-toggle="modal" class="editphoto" data-id="<?= $ID; ?>"><span class="fa fa-camera"></span></a></p>
                  <h3 class="profile-username text-center"><?= $MEMBERS; ?></h3>

                  <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                      <b>NUMBER</b> <a class="float-right"><?= $RECORD_NUMBER; ?></a>
                    </li>
                    <li class="list-group-item">
                      <b>STATUS </b>
                      <a class="float-right">
                        <?php
                        if ($row['STATUS'] == "PENDING") {
                          echo '<span class="text-warning">PENDING</span>';
                        } elseif ($row['STATUS'] == "APPROVED") {
                          echo '<span class="text-primary">APPROVED</span>';
                        } elseif ($row['STATUS'] == "DEACTIVE") {
                          echo '<span class="text-danger">DEACTIVE</span>';
                        } elseif ($row['STATUS'] == "REJECTED") {
                          echo '<span class="text-danger">REJECTED</span>';
                        } elseif ($row['STATUS'] == "ARCHIVED") {
                          echo '<span class="text-danger">ARCHIVED</span>';
                        }
                        ?>
                      </a>
                    </li>
                    <li class="list-group-item">
                      <b>REMARKS </b><br>
                      <a class="">
                        <span class=""><?= $row['STATUS_REMARKS']; ?></span>
                      </a>
                    </li>

                  </ul>

                </div>
                <!-- /.card-body -->
              </div>
            </div>

            <!-- /.col -->
            <div class="col-md-9">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                      <li class="nav-item">
                        <a class="nav-link active" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-home" aria-selected="true">Profile</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="pills-educational-tab" data-toggle="pill" href="#pills-educational" role="tab" aria-controls="pills-profile" aria-selected="false">Educational</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="pills-livelihood-tab" data-toggle="pill" href="#pills-livelihood" role="tab" aria-controls="pills-livelihood" aria-selected="false">Livelihood</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="pills-identity-tab" data-toggle="pill" href="#pills-identity" role="tab" aria-controls="pills-identity" aria-selected="false">Proof of Identity</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="pills-submitted-tab" data-toggle="pill" href="#pills-submitted" role="tab" aria-controls="pills-submitted" aria-selected="false">Proof of Livelihood</a>
                      </li>
                    </ul>
                  </h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div><!-- /.card-header -->
                <div class="card-body">

                  <div class="tab-content" id="pills-tabContent">
                    <!---Profile--->
                    <div class="tab-pane fade show active" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                      <table class="table table-bordered">
                        <tr>
                          <th colspan="2">RECORD_NUMBER: <?= $RECORD_NUMBER; ?></th>
                        </tr>
                        <tr>
                          <td width="180">NAME</td>
                          <td width="400"><?= $MEMBERS; ?></td>
                        </tr>
                        <tr>
                          <td>GENDER</td>
                          <td><?= $GENDER; ?></td>
                        </tr>
                        <tr>
                          <td>DATE OF BIRTH</td>
                          <td>
                            <?php
                            if ($DATE_OF_BIRTH == "") {
                              echo 'N/A';
                            } else {
                              echo date('l dS \o\f F Y', strtotime($DATE_OF_BIRTH));
                            }
                            ?>
                          </td>
                        </tr>
                        <tr>
                          <td>AGE</td>
                          <td><?= $AGE; ?></td>
                        </tr>
                        <tr>
                          <td>MOBILE </td>
                          <td><?= $MOBILE; ?></td>
                        </tr>
                        <tr>
                          <td> BARANGAY</td>
                          <td><?= $BARANGAY; ?></td>
                        </tr>
                        <tr>
                          <td> ADDRESS</td>
                          <td><?= $ADDRESS; ?></td>
                        </tr>
                      </table>
                    </div>
                    <!---EndProfile--->
                    <!---Educational--->
                    <div class="tab-pane fade" id="pills-educational" role="tabpanel" aria-labelledby="pills-educational-tab">
                      <table class="table table-bordered">
                        <tr>
                          <td width="180">HIGHEST EDUCATION</td>
                          <td><?= $EDUCATIONAL_BACKGROUND; ?></td>
                        </tr>
                        <tr>
                          <td> EMPLOYMENT HISTORY</td>
                          <td><?= $EMPLOYMENT_HISTORY; ?></td>
                        </tr>
                        <tr>
                          <td> SKILLS QUALIFICATION</td>
                          <td><?= $SKILLS_QUALIFICATION; ?></td>
                        </tr>
                      </table>
                    </div>
                    <!---End Educational--->
                    <!---Livelihoodd--->
                    <div class="tab-pane fade" id="pills-livelihood" role="tabpanel" aria-labelledby="pills-livelihood-tab">
                      <table class="table table-bordered">
                        <tr>
                          <td width="300"> DESIRED LIVELIHOOD PROGRAM</td>
                          <td><?= $DESIRED_LIVELIHOOD_PROGRAM; ?></td>
                        </tr>
                        <tr>
                          <td> EXPERIENCE IN LIVELIHOOD PROGRAM CHOSEN</td>
                          <td><?= $EXP_LIVELIHOOD_PROGRAM_CHOSEN; ?></td>
                        </tr>
                        <tr>
                          <td> CURRENT LIVELIHOOD SITUATION</td>
                          <td><?= $CURRENT_LIVELIHOOD_SITUATION; ?></td>
                        </tr>
                        <tr>
                          <td> REQUIRED TRAINING</td>
                          <td><?= $REQUIRED_TRAINING; ?></td>
                        </tr>
                        <tr>
                          <td> REASON INTERESTED IN LIVELIHOOD</td>
                          <td><?= $REASON_INTERESTED_IN_LIVELIHOOD; ?></td>
                        </tr>
                      </table>
                    </div>
                    <!---End of Livelihood--->
                    <!---Identity--->
                    <div class="tab-pane fade" id="pills-identity" role="tabpanel" aria-labelledby="pills-identity-tab">
                      <table class="table table-bordered">
                        <tr>
                          <td width="180">VALID ID</td>
                          <td><a href="" data-toggle="modal" data-target=".update_identity" data-jario="tooltip" data-placement="top" title="UPDATE PROOF OF IDENTITY"><span class="fa fa-edit"></span> <?= $VALID_ID_NUMBER; ?> </a></td>
                        </tr>
                        <tr>
                          <td>UPLOADED ID</td>
                          <td>
                            <a href="data:image/jpg;charset=utf8;base64,<?= base64_encode($UPLOAD_ID); ?>" data-toggle="lightbox" data-title="<?= $VALID_ID_NUMBER; ?>" data-gallery="gallery" width="500">
                              <img src="data:image/jpg;charset=utf8;base64,<?= base64_encode($UPLOAD_ID); ?>" width="100" height="100" class="img-thumbnail img-fluid mb-2">
                            </a>
                          </td>
                        </tr>
                        <tr>
                          <td>VALID ID WITH SELFIE</td>
                          <td>
                            <a href="data:image/jpg;charset=utf8;base64,<?= base64_encode($UPLOAD_WITH_SELFIE); ?>" data-toggle="lightbox" data-title="<?= $VALID_ID_NUMBER; ?>" data-gallery="gallery" width="500">
                              <img src="data:image/jpg;charset=utf8;base64,<?= base64_encode($UPLOAD_WITH_SELFIE); ?>" width="100" height="100" class="img-thumbnail img-fluid mb-2">
                            </a>
                          </td>
                        </tr>
                      </table>
                    </div>
                    <!---End of identity--->
                    <!---Submitted--->
                    <div class="tab-pane fade" id="pills-submitted" role="tabpanel" aria-labelledby="pills-submitted-tab">
                      <table id="example1" class="table table-bordered table-striped table-sm text-sm">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>DATE SUBMITTED</th>
                            <th>IMAGE ATTACHED</th>
                            <th>DESCRIPTION</th>
                            <th>STATUS</th>
                            <th>DATE ACTION</th>
                            <th>REMARKS</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $sqlr = "SELECT * FROM tbl_records WHERE MEMID='$ID' ORDER BY DATE_SUBMITTED DESC";
                          $queryr = $conn->query($sqlr);
                          $cnt = 1;
                          while ($prof = $queryr->fetch_assoc()) {
                            $image = $prof['PROF_LIVELIHOOD'];
                            $status = $prof['PROF_STATUS'];
                            if ($status == "PENDING") {
                              $stats = '<label class="text-warning">PENDING</label>';
                            } elseif ($status == "APPROVED") {
                              $stats = '<label class="text-primary">APPROVED</label>';
                            } else {
                              $stats = '<label class="text-danger">REJECTED</label>';
                            }
                          ?>
                            <tr>
                              <td><?= $cnt++; ?></td>
                              <td><?= $prof['DATE_SUBMITTED']; ?></td>
                              <td>
                                <a href="data:image/jpg;charset=utf8;base64,<?= base64_encode($image); ?>" data-toggle="lightbox" data-title="<?= $prof['PROF_DESCRIPTION']; ?>" data-gallery="gallery" width="500">
                                  <img src="data:image/jpg;charset=utf8;base64,<?= base64_encode($image); ?>" width="50" height="50" class="img-thumbnail img-fluid mb-2">
                              </td>
                              <td><?= $prof['PROF_DESCRIPTION']; ?></td>
                              <td><?= $stats; ?></td>
                              <td><?= $prof['PROF_UPDATED']; ?></td>
                              <td><?= $prof['PROF_REMARKS']; ?></td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                    <!---Enc Submitted--->
                  </div>

                </div><!-- /.card-body -->
                <div class="card-footer text-muted">
                  <div class="float-right"></div>
                </div>
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
    <div class="modal fade" id="baptised">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"> <span class="fa-solid fa fa-user"></span> Change Photo</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" method="POST" action="member_profile_update.php" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <input type="hidden" class="form-control" value="<?= $ID; ?>" name="ID">
                    <input type="hidden" class="form-control" name="member_info" value="<?= $ID; ?>" required>
                    <input type="hidden" class="form-control" name="record" value="<?= $row['RECORD_NUMBER']; ?>" required>
                    <input type="hidden" class="form-control" name="name" value="<?= str_replace(' ', '_', $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME']); ?>" required>
                    <label for="photo" class="control-label">Photo:</label>
                    <input class="form-control" name="image" type="file" id="formFileBaptised" onchange="previeww()"><br>
                    <img id="frameBaptised" src="" class="img-fluid " style="border-radius:10px">
                  </div>
                </div>

              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            <button type="submit" class="btn bg-primary btn-sm" name="upload"><i class="fa fa-check-square-o"></i> Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Add -->
    <div class="modal fade update_identity">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><span class="fa fa-plus"></span>UPLOAD VALID ID</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form class="form-horizontal needs-validation" action="member_prof_identity_update.php" method="POST" onSubmit="return valid();" enctype="multipart/form-data" novalidate>
            <div class="modal-body">
              <div class="row">
                <input type="hidden" class="form-control" name="member_info" value="<?= $ID; ?>" required>
                <input type="hidden" class="form-control" name="record" value="<?= $row['RECORD_NUMBER']; ?>" required>
                <input type="hidden" class="form-control" name="name" value="<?= str_replace(' ', '_', $row['LASTNAME'] . ', ' . $row['FIRSTNAME'] . ' ' . $row['MIDDLENAME']); ?>" required>
                <div class="col-lg-12">
                  <div class="form-group">
                    <label class="font-weight-normal">Lists of Valid ID</label>
                    <select style="width:100%" class="form-control select2 text-uppercase" name="VALID_ID_NUMBER" required>
                      <option value="<?= $VALID_ID_NUMBER; ?>" selected><?= $VALID_ID_NUMBER; ?></option>
                      <option>Philippine Passport</option>
                      <option>Philippine Driver’s License </option>
                      <option>Professional RegulatoryCommission (PRC) Card </option>
                      <option>Postal ID</option>
                      <option>Armed Forces of thePhilippines ID</option>
                      <option>Social Security System(SSS)</option>
                      <option>Government ServiceInsurance System (GSIS) </option>
                      <option>Unified Multi-Purpose ID </option>
                      <option>Phil Health ID </option>
                      <option>Tax Identification Number(TIN) Card </option>
                      <option>Persons with disability(PWD) Card </option>
                      <option>National ID </option>
                    </select>
                  </div>
                </div>

                <div class="col-lg-12">
                  <div class="form-group">
                    <label class="font-weight-normal">Upload valid ID</label>
                    <div class="custom-file">
                      <input type="file" name="UPLOAD_ID" id="UPLOAD_ID" class="form-control custom-file-input">
                      <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                  </div>
                </div>

                <div class="col-lg-12">
                  <div class="form-group">
                    <label class="font-weight-normal">Upload ID with Selfie</label>
                    <div class="custom-file">
                      <input type="file" name="UPLOAD_WITH_SELFIE" id="UPLOAD_WITH_SELFIE" class="form-control custom-file-input">
                      <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                  </div>
                </div>
              </div><!----row-->
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><i class="fa fa-times"></i> CLOSE</button>
              <button type="submit" class="btn btn-primary btn-sm" name="register"><i class="fa fa-save"></i> SUBMIT</button>

            </div>
          </form>
        </div>
      </div>
    </div>


    <?php @include "includes/footer.php"; ?>
    <script>
      function previeww() {
        frameBaptised.src = URL.createObjectURL(event.target.files[0]);
      }

      function clearImagee() {
        document.getElementById('formFileBaptised').value = null;
        frameBaptised.src = "";
      }
    </script>

</body>

</html>