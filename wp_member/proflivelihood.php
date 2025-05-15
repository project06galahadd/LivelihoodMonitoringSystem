<?php @include "includes/header.php"; ?>

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
              <h1>PROOF OF LIVELIHOOD </h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">HOME</a></li>
                <li class="breadcrumb-item active">DATA</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">

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

              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <button data-toggle="modal" data-memid='<?= $_SESSION['admin']; ?>' onclick="addSubDescription(this);" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> UPLOAD</button>
                  </h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                  <table id="example1" class="table table-bordered table-striped text-nowraps table-sm text-sm">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>DATE SUBMITTED</th>
                        <th>LIVELIHOOD PICTURE</th>
                        <th>DESCRIPTION</th>
                        <th>STATUS</th>
                        <th>DATE REMARKS</th>
                        <th>REMARKS</th>
                        <th>ACTIONS</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT * FROM tbl_records WHERE MEMID='" . $_SESSION['admin'] . "' ORDER BY DATE_SUBMITTED DESC";
                      $query = $conn->query($sql);
                      $cnt = 1;
                      while ($row = $query->fetch_assoc()) {
                        $image = $row['PROF_LIVELIHOOD'];
                        $status = $row['PROF_STATUS'];
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
                          <td><?= $row['DATE_SUBMITTED']; ?></td>
                          <td>
                            <a href="data:image/jpg;charset=utf8;base64,<?= base64_encode($image); ?>" data-toggle="lightbox" data-title="<?= $row['PROF_DESCRIPTION']; ?>" data-gallery="gallery" width="500">
                              <img src="data:image/jpg;charset=utf8;base64,<?= base64_encode($image); ?>" width="50" height="50" class="img-thumbnail img-fluid mb-2">
                          </td>
                          <td><?= $row['PROF_DESCRIPTION']; ?></td>
                          <td><?= $stats; ?></td>
                          <td><?= $row['PROF_UPDATED']; ?></td>
                          <td><?= $row['PROF_REMARKS']; ?></td>
                          <td align="right">
                            <div class="btn-group">
                              <button class="btn btn-primary btn-sm"
                                data-subattid="<?= $row['RECID']; ?>"
                                data-subdescattname="<?= $row['PROF_DESCRIPTION']; ?>"
                                data-subattyear="<?= $row['PROF_STATUS']; ?>"
                                data-subattfemale="<?= $row['PROF_REMARKS']; ?>"
                                onclick="subeditAttribute(this);" data-jario="tooltip" data-placement="top" title="EDIT"><i class="fa-solid fa fa-edit"></i> </button>
                              <button class="btn btn-danger btn-sm"
                                data-del_subattid="<?= $row['RECID']; ?>"
                                onclick="deleteSub(this);" data-jario="tooltip" data-placement="top" title="DELETE"><i class="fa-solid fa fa-trash"></i> </button>
                            </div>
                          </td>
                        </tr>
                      <?php
                        $cnt++;
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <?php include "includes/proflivelihood_modal.php"; ?>
    <?php include "includes/footer.php"; ?>